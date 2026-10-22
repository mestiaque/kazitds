<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Customer;
use ME\Kazitds\Models\Due;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use ME\Kazitds\Http\Services\CustomerMergeService;
use ME\Kazitds\Http\Services\CustomerSaleLedgerService;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:customer.view')->only(['index', 'show']);
        $this->middleware('authorization:customer.create')->only(['create', 'store']);
        $this->middleware('authorization:customer.edit')->only(['edit', 'update']);
        $this->middleware('authorization:customer.delete')->only('destroy');
        $this->middleware('authorization:customer.edit')->only('duplicates');
        $this->middleware('authorization:customer.delete')->only('merge');
    }

    public function duplicates(CustomerMergeService $mergeService)
    {
        $duplicateGroups = $mergeService->findDuplicateGroups();

        return view('kazitds::customers.duplicates', compact('duplicateGroups'));
    }

    public function merge(Request $request, CustomerMergeService $mergeService)
    {
        $validated = $request->validate([
            'keep_id' => 'required|integer|exists:customers,id',
            'merge_ids' => 'required|array|min:1',
            'merge_ids.*' => 'integer|distinct|exists:customers,id|different:keep_id',
        ]);

        $keptCustomer = Customer::findOrFail($validated['keep_id']);
        $mergeService->merge($keptCustomer, $validated['merge_ids']);

        return redirect()->route('customers.duplicates')
            ->with('success', __('kazitds::kazitds.customers_merged_successfully', ['name' => $keptCustomer->name]));
    }

    public function index(Request $request)
    {

        $query = Customer::query();

        if ($request->has('name') && $request->input('name') != '') {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('phone') && $request->input('phone') != '') {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }

        $customers = $query->paginate(get_setting('pagination', 10));


        $editItem = $request->filled('edit') ? Customer::find($request->input('edit')) : null;
        $duplicateGroupCount = app(CustomerMergeService::class)->findDuplicateGroups()->count();

        return view('kazitds::customers.index', compact('customers', 'editItem', 'duplicateGroupCount'));
    }

    public function create()
    {
        return redirect()->route('customers.index', ['create' => 1]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'due_amount' => 'nullable|numeric|min:0',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', __('kazitds::kazitds.Customer created successfully.'));
    }

    public function show(Customer $customer)
    {
        // Sales recorded before due tracking began have no payment records, so they
        // are left out of the statement; whatever they left unpaid is the opening balance.
        $dueTrackingStartedAt = Due::min('created_at');
        $hasSalesBeforeDueTracking = $dueTrackingStartedAt
            && $customer->sales()->where('created_at', '<', $dueTrackingStartedAt)->exists();

        $sales = $customer->sales()
            ->when($dueTrackingStartedAt, fn ($query) => $query->where('created_at', '>=', $dueTrackingStartedAt))
            ->orderBy('sale_date')
            ->orderBy('id')
            ->get();
        $payments = $customer->paymentHistories()
            ->with('sale:id,invoice_number')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $saleTransactions = $sales->map(function ($sale) {
            $amount = round((float) $sale->total_amount - (float) $sale->discount, 2);

            return [
                'date' => $sale->sale_date,
                'sort_at' => $sale->sale_date->getTimestamp(),
                'sort_order' => 0,
                'sort_id' => $sale->id,
                'type' => 'sale',
                'details' => trim(__('kazitds::kazitds.Sale') . ' ' . ($sale->invoice_number ?: '#' . $sale->id) . ' ' . ($sale->notes ?? '')),
                'sale_id' => $sale->id,
                'debit' => max($amount, 0),
                'credit' => max(-$amount, 0),
            ];
        });

        $saleTimestamps = $saleTransactions->pluck('sort_at', 'sale_id');

        $paymentTransactions = $payments->map(function ($payment) use ($saleTimestamps) {
            $details = array_filter([
                __('kazitds::kazitds.Payment'),
                $payment->sale
                    ? __('kazitds::kazitds.Invoice') . ': ' . ($payment->sale->invoice_number ?: '#' . $payment->sale_id)
                    : null,
                $payment->payment_method ? __($payment->payment_method) : null,
                $payment->note,
            ]);
            $date = $payment->created_at;

            return [
                'date' => $date,
                // A payment taken with a sale can be saved a few seconds before the sale's own
                // timestamp; keep it after its sale so the running balance never dips falsely.
                'sort_at' => max($date->getTimestamp(), $saleTimestamps->get($payment->sale_id, 0)),
                'sort_order' => 1,
                'sort_id' => $payment->id,
                'type' => 'payment',
                'details' => implode(' - ', $details),
                'sale_id' => $payment->sale_id,
                'debit' => 0,
                'credit' => (float) $payment->amount,
            ];
        });

        $transactions = $saleTransactions->concat($paymentTransactions);
        $totalDebit = round((float) $transactions->sum('debit'), 2);
        $totalCredit = round((float) $transactions->sum('credit'), 2);
        $openingBalance = round((float) $customer->due_amount - $totalDebit + $totalCredit, 2);

        if (abs($openingBalance) >= 0.005) {
            $firstTransaction = $transactions->sortBy('sort_at')->first();
            $openingDate = ($firstTransaction['date'] ?? $customer->created_at)->copy();
            $transactions->push([
                'date' => $openingDate,
                'sort_at' => $firstTransaction['sort_at'] ?? $openingDate->getTimestamp(),
                'sort_order' => -1,
                'sort_id' => 0,
                'type' => 'opening',
                'details' => __('kazitds::kazitds.Opening Balance'),
                'sale_id' => null,
                'debit' => max($openingBalance, 0),
                'credit' => max(-$openingBalance, 0),
            ]);
        }

        $transactions = $transactions
            ->sort(function ($a, $b) {
                return [$a['sort_at'], $a['sort_order'], $a['sort_id']]
                    <=> [$b['sort_at'], $b['sort_order'], $b['sort_id']];
            })
            ->values();

        $balance = 0;
        $transactions = $transactions->map(function ($transaction) use (&$balance) {
            $balance = round($balance + $transaction['debit'] - $transaction['credit'], 2);
            $transaction['balance'] = $balance;

            return $transaction;
        })->reverse()->values();

        $perPage = max(1, (int) get_setting('pagination', 10));
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginatedTransactions = new LengthAwarePaginator(
            $transactions->forPage($page, $perPage)->values(),
            $transactions->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]
        );

        return view('kazitds::customers.show', [
            'customer' => $customer,
            'transactions' => $paginatedTransactions,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'dueTrackingStartedAt' => $hasSalesBeforeDueTracking ? $dueTrackingStartedAt : null,
        ]);
    }

    public function edit(Customer $customer)
    {
        return redirect()->route('customers.index', ['edit' => $customer->id]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'due_amount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($customer, $validated) {
            $targetDueAmount = (float) ($validated['due_amount'] ?? 0);
            $customer->update($validated);

            app(CustomerSaleLedgerService::class)->applyManualDueAmount($customer->id, $targetDueAmount);
        });

        return redirect()->route('customers.index')
            ->with('success', __('kazitds::kazitds.Customer updated successfully.'));
    }

    public function destroy(Customer $customer)
    {
        // যদি কোনো sales থাকে তাহলে ডিলেট না করতে চাও:
        if ($customer->sales()->count() > 0) {
            return redirect()->route('customers.index')
                ->withErrors(__('kazitds::kazitds.Cannot delete customer with existing sales.'));
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', __('kazitds::kazitds.Customer deleted successfully.'));
    }
}
