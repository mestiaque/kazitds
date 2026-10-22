<?php

namespace ME\Kazitds\Http\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ME\Kazitds\Models\Customer;

class CustomerMergeService
{
    public function __construct(public CustomerSaleLedgerService $ledgerService)
    {
    }

    /**
     * Group customers that look like the same person: same name once spaces and
     * punctuation are ignored, or the same phone number (Bangla or English digits).
     *
     * @return Collection<int, array{customers: Collection<int, Customer>, reasons: array<int, string>}>
     */
    public function findDuplicateGroups(): Collection
    {
        return $this->groupDuplicates(Customer::withCount('sales')->orderBy('id')->get());
    }

    /**
     * @param  Collection<int, Customer>  $customers
     * @return Collection<int, array{customers: Collection<int, Customer>, reasons: array<int, string>}>
     */
    public function groupDuplicates(Collection $customers): Collection
    {
        $parent = $customers->pluck('id', 'id')->all();
        $reasons = [];

        $find = function (int $id) use (&$parent, &$find): int {
            return $parent[$id] === $id ? $id : $parent[$id] = $find($parent[$id]);
        };

        foreach (['name' => fn (Customer $customer) => $this->nameKey($customer->name), 'phone' => fn (Customer $customer) => $this->phoneKey($customer->phone)] as $reason => $keyOf) {
            $customers->groupBy($keyOf)
                ->filter(fn (Collection $group, $key) => $key !== '' && $group->count() > 1)
                ->each(function (Collection $group) use (&$parent, &$reasons, $find, $reason) {
                    $rootId = $find($group->first()->id);
                    foreach ($group as $customer) {
                        $parent[$find($customer->id)] = $rootId;
                        $reasons[$customer->id][$reason] = $reason;
                    }
                });
        }

        return $customers
            ->groupBy(fn (Customer $customer) => $find($customer->id))
            ->filter(fn (Collection $group) => $group->count() > 1)
            ->map(fn (Collection $group) => [
                'customers' => $group->values(),
                'reasons' => $group->flatMap(fn (Customer $customer) => $reasons[$customer->id] ?? [])->unique()->values()->all(),
            ])
            ->values();
    }

    /**
     * Move everything that belongs to the duplicate customers onto the kept customer,
     * then delete the duplicates. The kept customer's due becomes the sum of all dues.
     *
     * @param  array<int, int>  $duplicateIds
     */
    public function merge(Customer $keptCustomer, array $duplicateIds): void
    {
        $duplicates = Customer::whereIn('id', $duplicateIds)->whereKeyNot($keptCustomer->id)->get();

        if ($duplicates->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($keptCustomer, $duplicates) {
            $targetDueAmount = (float) $keptCustomer->due_amount + (float) $duplicates->sum('due_amount');
            $duplicateIds = $duplicates->pluck('id')->all();

            foreach ($this->tablesWithCustomerId() as $table) {
                DB::table($table)->whereIn('customer_id', $duplicateIds)->update(['customer_id' => $keptCustomer->id]);
            }

            DB::table('sales')
                ->where('customer_id', $keptCustomer->id)
                ->whereIn('customer_name', $duplicates->pluck('name')->all())
                ->update(['customer_name' => $keptCustomer->name]);

            if (Schema::hasColumns('user_activities', ['subject_type', 'subject_id'])) {
                DB::table('user_activities')
                    ->where('subject_type', Customer::class)
                    ->whereIn('subject_id', $duplicateIds)
                    ->update(['subject_id' => $keptCustomer->id]);
            }

            foreach (['phone', 'email', 'address'] as $field) {
                if (blank($keptCustomer->{$field})) {
                    $keptCustomer->{$field} = $duplicates->first(fn (Customer $duplicate) => filled($duplicate->{$field}))?->{$field};
                }
            }
            $keptCustomer->save();

            Customer::whereIn('id', $duplicateIds)->delete();

            $this->ledgerService->applyManualDueAmount($keptCustomer->id, $targetDueAmount);
        });
    }

    /**
     * @return array<int, string>
     */
    protected function tablesWithCustomerId(): array
    {
        return collect(Schema::getTableListing(schemaQualified: false))
            ->reject(fn (string $table) => $table === 'customers')
            ->filter(fn (string $table) => Schema::hasColumn($table, 'customer_id'))
            ->values()
            ->all();
    }

    protected function nameKey(?string $name): string
    {
        return Str::lower(preg_replace('/[\s\p{P}]+/u', '', (string) $name));
    }

    protected function phoneKey(?string $phone): string
    {
        $digits = preg_replace('/\D/', '', strtr((string) $phone, [
            '০' => '0', '১' => '1', '২' => '2', '৩' => '3', '৪' => '4',
            '৫' => '5', '৬' => '6', '৭' => '7', '৮' => '8', '৯' => '9',
        ]));

        return strlen($digits) >= 6 ? substr($digits, -11) : '';
    }
}
