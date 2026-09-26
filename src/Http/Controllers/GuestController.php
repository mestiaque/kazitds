<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Sale;

class GuestController extends Controller
{
    public function saleInvoice($invoice_number)
    {
        $sale = Sale::firstWhere('invoice_number', $invoice_number);
        $sale->load('customer', 'items.productVariant.product', 'items.productVariant.brand', 'items.productVariant.pack');
        return view('kazitds::sales.invoice', compact('sale'));
    }
}
