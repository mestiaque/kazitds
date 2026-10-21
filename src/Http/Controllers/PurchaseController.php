<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Purchase;
use ME\Kazitds\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $query = Purchase::with('productVariant.product', 'productVariant.brand', 'productVariant.pack', 'supplier', 'creator', 'updater');

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

        $purchases = $query->orderBy('purchase_date', 'desc')->orderByDesc('id')
            ->paginate(get_setting('pagination', 10))
            ->withQueryString();
        $productVariants = ProductVariant::with('product', 'brand', 'pack')->get();
        $suppliers = Supplier::all();

        // A purchase opened by ?show= / ?edit= gets its modals even when it's on another page
        $focusId = $request->input('show', $request->input('edit'));
        $focusItem = $focusId
            ? Purchase::with('productVariant.product', 'productVariant.brand', 'productVariant.pack', 'supplier', 'creator', 'updater')->find($focusId)
            : null;

        return view('kazitds::purchases.index', compact('purchases', 'productVariants', 'suppliers', 'focusItem'));
    }

    public function create()
    {
        // Add happens in a modal on the purchase list
        return redirect()->route('purchases.index', array_filter(['create' => 1, 'product_variant_id' => request('product_variant_id')]));
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

        // Purchase number comes from the row's own id, so two purchases saved at once can't share a number
        DB::transaction(function () use ($data) {
            $purchase = Purchase::create($data);
            $purchase->update(['purchase_number' => str_pad($purchase->id, 8, '0', STR_PAD_LEFT)]);
        });
        return redirect()->route('purchases.index')
            ->with('success', __('kazitds::kazitds.Purchase recorded successfully.'));
    }

    public function show(Purchase $purchase)
    {
        return redirect()->route('purchases.index', ['show' => $purchase->id]);
    }

    public function edit(Purchase $purchase)
    {
        return redirect()->route('purchases.index', ['edit' => $purchase->id]);
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
