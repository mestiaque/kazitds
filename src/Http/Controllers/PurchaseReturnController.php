<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Purchase;
use ME\Kazitds\Models\PurchaseReturn;
use ME\Kazitds\Models\ProductVariant;
use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:purchase_return.view')->only(['index', 'show']);
        $this->middleware('authorization:purchase_return.create')->only(['create', 'store']);
        $this->middleware('authorization:purchase_return.edit')->only(['edit', 'update']);
        $this->middleware('authorization:purchase_return.delete')->only('destroy');
    }

    public function index()
    {
        $purchaseReturns = PurchaseReturn::with(['purchase.supplier'])
            ->latest()
            ->paginate(get_setting('pagination', 10))
;

        return view('kazitds::purchase_returns.index', compact('purchaseReturns'));
    }

    public function create()
    {
        $purchases = Purchase::with('supplier')->latest()->get();
        return view('kazitds::purchase_returns.create', compact('purchases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'product_variant_id' => 'required|exists:product_variants,id',
            'returned_quantity' => 'required|integer|min:1',
            'return_price_per_unit' => 'required|numeric|min:0',
            'return_date' => 'required|date',
            'status' => 'in:pending,approved,rejected',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validate that the returned quantity doesn't exceed the originally purchased quantity
        $purchase = Purchase::findOrFail($request->purchase_id);
        if ($purchase->product_variant_id != $request->product_variant_id) {
            return back()->withErrors(['product_variant_id' => 'This product was not purchased in the selected purchase.']);
        }

        $alreadyReturned = PurchaseReturn::where('purchase_id', $request->purchase_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->sum('returned_quantity');

        if (($alreadyReturned + $request->returned_quantity) > $purchase->quantity) {
            return back()->withErrors(['returned_quantity' => 'Cannot return more than originally purchased quantity.']);
        }

        // Check if there's enough stock available to return
        $productVariant = ProductVariant::findOrFail($request->product_variant_id);
        $currentStock = $productVariant->getCurrentStock();

        if ($currentStock < $request->returned_quantity) {
            return back()->withErrors(['returned_quantity' => "Insufficient stock available. Current stock: {$currentStock}"]);
        }

        $totalReturnAmount = $request->returned_quantity * $request->return_price_per_unit;

        PurchaseReturn::create([
            'return_number' => PurchaseReturn::generateReturnNumber(),
            'purchase_id' => $request->purchase_id,
            'product_variant_id' => $request->product_variant_id,
            'returned_quantity' => $request->returned_quantity,
            'return_price_per_unit' => $request->return_price_per_unit,
            'total_return_amount' => $totalReturnAmount,
            'total_amount' => $totalReturnAmount,
            'return_date' => $request->return_date,
            'status' => $request->status ?? 'pending',
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        return redirect()->route('purchase-returns.index')
            ->with('success', 'Purchase return created successfully.');
    }

    public function show($id)
    {
        $purchaseReturn = PurchaseReturn::with(['purchase.supplier'])->findOrFail($id);
        return view('kazitds::purchase_returns.show', compact('purchaseReturn'));
    }

    public function edit($id)
    {
        $purchaseReturn = PurchaseReturn::findOrFail($id);
        $purchases = Purchase::with('supplier')->latest()->get();
        return view('kazitds::purchase_returns.edit', compact('purchaseReturn', 'purchases'));
    }

    public function update(Request $request, $id)
    {
        $purchaseReturn = PurchaseReturn::findOrFail($id);

        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'product_variant_id' => 'required|exists:product_variants,id',
            'returned_quantity' => 'required|integer|min:1',
            'return_price_per_unit' => 'required|numeric|min:0',
            'return_date' => 'required|date',
            'status' => 'in:pending,approved,rejected',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validate that the returned quantity doesn't exceed the originally purchased quantity
        $purchase = Purchase::findOrFail($request->purchase_id);
        if ($purchase->product_variant_id != $request->product_variant_id) {
            return back()->withErrors(['product_variant_id' => 'This product was not purchased in the selected purchase.']);
        }

        $alreadyReturned = PurchaseReturn::where('purchase_id', $request->purchase_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->where('id', '!=', $id)
            ->sum('returned_quantity');

        if (($alreadyReturned + $request->returned_quantity) > $purchase->quantity) {
            return back()->withErrors(['returned_quantity' => 'Cannot return more than originally purchased quantity.']);
        }

        // Check if there's enough stock available to return (only if increasing quantity or changing product)
        $productVariant = ProductVariant::findOrFail($request->product_variant_id);
        $currentStock = $productVariant->getCurrentStock();

        // Add back the current return quantity to get actual available stock
        if ($purchaseReturn->status == 'approved') {
            $currentStock += $purchaseReturn->returned_quantity;
        }

        if ($currentStock < $request->returned_quantity) {
            return back()->withErrors(['returned_quantity' => "Insufficient stock available. Current stock: {$currentStock}"]);
        }

        $totalReturnAmount = $request->returned_quantity * $request->return_price_per_unit;

        $purchaseReturn->update([
            'purchase_id' => $request->purchase_id,
            'product_variant_id' => $request->product_variant_id,
            'returned_quantity' => $request->returned_quantity,
            'return_price_per_unit' => $request->return_price_per_unit,
            'total_return_amount' => $totalReturnAmount,
            'total_amount' => $totalReturnAmount,
            'return_date' => $request->return_date,
            'status' => $request->status,
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        return redirect()->route('purchase-returns.index')
            ->with('success', 'Purchase return updated successfully.');
    }

    public function destroy($id)
    {
        $purchaseReturn = PurchaseReturn::findOrFail($id);
        $purchaseReturn->delete();
        return redirect()->route('purchase-returns.index')
            ->with('success', 'Purchase return deleted successfully.');
    }
}
