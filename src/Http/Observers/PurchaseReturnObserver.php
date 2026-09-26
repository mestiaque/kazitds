<?php

namespace ME\Kazitds\Http\Observers;

use ME\Kazitds\Models\PurchaseReturn;
use Illuminate\Support\Facades\Log;

class PurchaseReturnObserver
{
    /**
     * Handle the PurchaseReturn "created" event.
     */
    public function created(PurchaseReturn $purchaseReturn): void
    {
        // No automatic stock update on creation, only when approved
    }

    /**
     * Handle the PurchaseReturn "updated" event.
     */
    public function updated(PurchaseReturn $purchaseReturn): void
    {
        // If status changed to approved, stock will be automatically calculated via getCurrentStock()
        // If status changed from approved to rejected/pending, stock will also be recalculated

        // Log the stock change for audit purposes if needed
        if ($purchaseReturn->isDirty('status')) {
            Log::info("Purchase return {$purchaseReturn->return_number} status changed to {$purchaseReturn->status}");
        }
    }

    /**
     * Handle the PurchaseReturn "deleted" event.
     */
    public function deleted(PurchaseReturn $purchaseReturn): void
    {
        // Stock will be automatically recalculated since the return record is removed
    }
}
