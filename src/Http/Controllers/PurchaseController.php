<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Purchase;
use ME\Kazitds\Models\Supplier;
use Illuminate\Http\Request;
use ME\Kazitds\Models\ProductVariant;

class PurchaseController extends Controller
{

    public function __construct()
    {
        $this->middleware('authorization:purchase.view')->only(['index', 'show']);
        $this->middleware('authorization:purchase.create')->only(['create', 'store']);
        $this->middleware('authorization:purchase.edit')->only(['edit', 'update']);
        $this->middleware('authorization:purchase.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Purchase::with('productVariant.product', 'productVariant.brand', 'productVariant.pack', 'supplier');

        // Apply filters
        if ($request->has('product_variant') && $request->product_variant) {
            $query->where('product_variant_id', $request->product_variant);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->paginate(get_setting('pagination', 10))
;
        $productVariants = ProductVariant::with('product', 'brand', 'pack')->get();

        return view('kazitds::purchases.index', compact('purchases', 'productVariants'));
    }

    public function create()
    {
        $productVariants = ProductVariant::with('product', 'brand', 'pack')->get();
        $suppliers = Supplier::all();
        return view('kazitds::purchases.create', compact('productVariants', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'price_per_unit' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $data = $request->all();
        $data['total_price'] = $request->quantity * $request->price_per_unit;
        $data['created_by'] = auth()->id();

        // Generate sequential purchase number
        $lastPurchase = Purchase::orderBy('id', 'desc')->first();
        $nextNumber = $lastPurchase ? intval($lastPurchase->id) + 1 : 1;
        $data['purchase_number'] = str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        Purchase::create($data);
        return redirect()->route('purchases.index')
            ->with('success', __('kazitds::kazitds.Purchase recorded successfully.'));
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('productVariant.product', 'productVariant.brand', 'productVariant.pack');
        return view('kazitds::purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $productVariants = ProductVariant::with('product', 'brand', 'pack')->get();
        $suppliers = Supplier::all();
        return view('kazitds::purchases.edit', compact('purchase', 'productVariants', 'suppliers'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'price_per_unit' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $data = $request->all();
        $data['total_price'] = $request->quantity * $request->price_per_unit;
        $data['updated_by'] = auth()->id();

        // Don't change purchase number on update

        $purchase->update($data);
        return redirect()->route('purchases.index')
            ->with('success', __('kazitds::kazitds.(Purchase updated successfully).'));
    }

    public function destroy(Purchase $purchase)
    {
        try {
            // Load product variant
            $purchase->load('productVariant');
            $variant = $purchase->productVariant;

            // Get current stock and purchase quantity
            $currentStock = $variant->getCurrentStock();
            $purchaseQuantity = $purchase->quantity;

            // Check if the current stock is less than the purchase quantity
            if ($currentStock < $purchaseQuantity) {
                $productName = $variant->product->name;
                $brandName = $variant->brand ? $variant->brand->name : 'N/A';
                $packName = $variant->pack ? $variant->pack->name : 'N/A';

                return redirect()->route('purchases.index')
                                ->withErrors(__('kazitds::kazitds.purchase_delete_blocked', [
                                    'productName'      => $productName,
                                    'brandName'        => $brandName,
                                    'packName'         => $packName,
                                    'currentStock'     => $currentStock,
                                    'purchaseQuantity' => $purchaseQuantity,
                                ]));
            }

            // If stock is sufficient, proceed with deletion
            $purchase->delete();
            return redirect()->route('purchases.index')
                ->with('success', __('kazitds::kazitds.(Purchase deleted successfully).'));

        } catch (\Exception $e) {
            return redirect()->route('purchases.index')
                ->withErrors($e->getMessage());
        }
    }
}
