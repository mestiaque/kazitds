<?php

namespace ME\Kazitds\Http\Observers;

use ME\Kazitds\Models\SaleReturn;
use Illuminate\Support\Facades\Log;

class SaleReturnObserver
{
    /**
     * Handle the SaleReturn "created" event.
     */
    public function created(SaleReturn $saleReturn): void
    {
        // If the sale return is created with approved status, update the stock immediately
        if ($saleReturn->status === 'approved') {
            $this->updateStock($saleReturn);
            Log::info("Sale return {$saleReturn->return_number} created with approved status - stock updated");
        }
    }

    /**
     * Handle the SaleReturn "updated" event.
     */
    public function updated(SaleReturn $saleReturn): void
    {
        // If status changed, handle stock updates
        if ($saleReturn->isDirty('status')) {
            Log::info("Sale return {$saleReturn->return_number} status changed to {$saleReturn->status}");

            // If status changed to approved, update the stock
            if ($saleReturn->status === 'approved') {
                $this->updateStock($saleReturn);
                Log::info("Stock increased for return {$saleReturn->return_number}");
            }

            // If status changed from approved to something else, the stock will be
            // automatically recalculated via ProductVariant::getCurrentStock()
        }
    }

    /**
     * Update stock when a sale return is approved
     */
    private function updateStock(SaleReturn $saleReturn): void
    {
        // Load the product variant if not already loaded
        if (!$saleReturn->relationLoaded('productVariant')) {
            $saleReturn->load('productVariant');
        }

        // ProductVariant::getCurrentStock() already accounts for approved sale returns,
        // so we don't need to manually adjust stock. The method will automatically
        // include this return in its calculations if status is approved.

        // Let's still get the stock for logging purposes
        $currentStock = $saleReturn->productVariant->getCurrentStock();

        Log::info("Product variant ID {$saleReturn->product_variant_id} stock is now {$currentStock} " .
                 "after return of {$saleReturn->returned_quantity} units");
    }

    /**
     * Handle the SaleReturn "deleted" event.
     */
    public function deleted(SaleReturn $saleReturn): void
    {
        // Stock will be automatically recalculated since the return record is removed
    }
}
