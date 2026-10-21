<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Due;
use ME\Kazitds\Models\Sale;

class GuestController extends Controller
{
    public function saleInvoice($token)
    {
        // Looked up by the random token only; the invoice number is guessable
        $sale = Sale::with('customer', 'items.productVariant.product', 'items.productVariant.brand', 'items.productVariant.pack')
            ->firstWhere('public_token', $token);

        if (! $sale) {
            return response()->view('kazitds::sales.invoice-not-found', [], 404);
        }

        // The invoice summary (subtotal, previous due, paid, due) comes from the sale's due record
        $dues = Due::where('sale_id', $sale->id)->first();

        return view('kazitds::sales.invoice', compact('sale', 'dues'));
    }
}
