<?php

namespace ME\Kazitds\Http\Services;

use ME\Kazitds\Models\Customer;
use ME\Kazitds\Models\Due;
use ME\Kazitds\Models\Sale;

class CustomerSaleLedgerService
{
    /**
     * Recalculate all sale-related ledger fields for one customer in chronological order.
     */
    public function recalculateForCustomer(int $customerId): void
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return;
        }

        $sales = Sale::with(['items', 'paymentHistories'])
            ->where('customer_id', $customerId)
            ->orderBy('sale_date')
            ->orderBy('id')
            ->get();

        // First sale previous_due is treated as opening carry-forward due.
        $runningDue = (float) ($sales->first()?->previous_due ?? 0);
        $activeSaleIds = [];

        foreach ($sales as $sale) {
            $totalAmount = (float) $sale->items->sum('total_price');
            $discount = (float) ($sale->discount ?? 0);
            $netAmount = $totalAmount - $discount;
            $paidAmount = (float) $sale->paymentHistories->sum('amount');

            $previousDue = $runningDue;
            $dueAfterPayment = $previousDue + $netAmount - $paidAmount;

            $sale->update([
                'total_amount' => $totalAmount,
                'previous_due' => $previousDue,
                'net_amount' => $netAmount,
            ]);

            Due::updateOrCreate(
                ['sale_id' => $sale->id],
                [
                    'customer_id' => $customerId,
                    'sale_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'due' => $dueAfterPayment,
                    'previous_due' => $previousDue,
                    'total_due' => $dueAfterPayment,
                ]
            );

            $runningDue = $dueAfterPayment;
            $activeSaleIds[] = $sale->id;
        }

        // Remove stale due rows that are no longer tied to an existing sale.
        $staleDueQuery = Due::where('customer_id', $customerId);
        if (!empty($activeSaleIds)) {
            $staleDueQuery->whereNotIn('sale_id', $activeSaleIds);
        }
        $staleDueQuery->delete();

        $customer->update([
            'due_amount' => $runningDue,
        ]);
    }

    /**
     * Apply a manual due amount change from customer form and propagate everywhere.
     */
    public function applyManualDueAmount(int $customerId, float $targetDueAmount): void
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            return;
        }

        $this->recalculateForCustomer($customerId);
        $currentDueAmount = (float) $customer->fresh()->due_amount;
        $delta = $targetDueAmount - $currentDueAmount;

        if (abs($delta) < 0.0001) {
            return;
        }

        $firstSale = Sale::where('customer_id', $customerId)
            ->orderBy('sale_date')
            ->orderBy('id')
            ->first();

        // No sale yet: only customer-level due exists.
        if (!$firstSale) {
            $customer->update(['due_amount' => $targetDueAmount]);
            return;
        }

        $firstSale->update([
            'previous_due' => (float) ($firstSale->previous_due ?? 0) + $delta,
        ]);

        $this->recalculateForCustomer($customerId);
    }
}
