<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Sale;
use ME\Kazitds\Models\SaleReturn;
use ME\Kazitds\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SaleReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:sale_return.view')->only(['index', 'show']);
        $this->middleware('authorization:sale_return.create')->only(['create', 'store']);
        $this->middleware('authorization:sale_return.edit')->only(['edit', 'update']);
        $this->middleware('authorization:sale_return.delete')->only('destroy');
    }

    public function index()
    {
        $saleReturns = SaleReturn::with(['sale.customer'])
            ->latest()
            ->paginate(get_setting('pagination', 10))
;

        return view('kazitds::sale_returns.index', compact('saleReturns'));
    }

    public function create()
    {
        $sales = Sale::with('customer')->latest()->get();
        return view('kazitds::sale_returns.create', compact('sales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'return_date' => 'required|date',
            'status' => 'in:pending,approved,rejected',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'total_amount' => 'required|numeric|min:0',
        ]);

        // Find the sale and its items
        $sale = Sale::with('items.productVariant')->findOrFail($request->sale_id);

        // Generate return number
        $returnNumber = SaleReturn::generateReturnNumber();

        // Calculate actual total return amount
        $calculatedTotalAmount = 0;
        $returnItems = [];

        // First, prepare all return items and calculate the total
        foreach ($sale->items as $saleItem) {
            // Check for already returned quantities
            $alreadyReturned = SaleReturn::where('sale_id', $request->sale_id)
                ->where('product_variant_id', $saleItem->product_variant_id)
                ->sum('returned_quantity');

            $remainingQuantity = $saleItem->quantity - $alreadyReturned;

            if ($remainingQuantity <= 0) {
                continue; // Skip if this item has already been fully returned
            }

            $itemReturnAmount = $remainingQuantity * $saleItem->price_per_unit;
            $calculatedTotalAmount += $itemReturnAmount;

            $returnItems[] = [
                'product_variant_id' => $saleItem->product_variant_id,
                'quantity' => $remainingQuantity,
                'price' => $saleItem->price_per_unit,
                'amount' => $itemReturnAmount
            ];
        }

        // If the submitted total doesn't match what we calculated, use the calculated value
        // This ensures consistency between the individual item returns and the total
        $totalReturnAmount = $calculatedTotalAmount > 0 ? $calculatedTotalAmount : $request->total_amount;

        // Create a return record for each sale item automatically
        $returnCreated = false;

        foreach ($returnItems as $item) {
            // Create a return for this item
            SaleReturn::create([
                'return_number' => $returnNumber, // Same return number for all items in this transaction
                'sale_id' => $request->sale_id,
                'product_variant_id' => $item['product_variant_id'],
                'returned_quantity' => $item['quantity'],
                'return_price_per_unit' => $item['price'],
                'total_return_amount' => $item['amount'],
                'total_amount' => $totalReturnAmount, // Use our calculated total amount
                'return_date' => $request->return_date,
                'status' => $request->status ?? 'pending',
                'reason' => $request->reason,
                'notes' => $request->notes,
            ]);

            $returnCreated = true;
        }

        if (!$returnCreated) {
            return back()->withErrors(['sale_id' => 'All items in this sale have already been returned.']);
        }

        return redirect()->route('sale-returns.index')
            ->with('success', 'Sale return created successfully with all items from the selected invoice.');
    }

    public function show($id)
    {
        $saleReturn = SaleReturn::with(['sale.customer', 'productVariant.product'])->findOrFail($id);
        // Get all return items with the same return number
        $relatedReturns = SaleReturn::where('return_number', $saleReturn->return_number)
            ->with('productVariant.product')
            ->get();

        return view('kazitds::sale_returns.show', compact('saleReturn', 'relatedReturns'));
    }

    public function edit($id)
    {
        $saleReturn = SaleReturn::findOrFail($id);
        $sales = Sale::with('customer')->latest()->get();
        return view('kazitds::sale_returns.edit', compact('saleReturn', 'sales'));
    }

    public function update(Request $request, $id)
    {
        $saleReturn = SaleReturn::findOrFail($id);

        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'return_date' => 'required|date',
            'status' => 'in:pending,approved,rejected',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'total_amount' => 'nullable|numeric|min:0',
        ]);

        // For status changes, we only need to update the status
        if ($request->has('status') && !$request->has('product_variant_id')) {
            // This is a status update only
            $saleReturn->update([
                'status' => $request->status,
                'notes' => $request->notes ?? $saleReturn->notes,
            ]);

            return redirect()->route('sale-returns.index')
                ->with('success', 'Sale return status updated successfully.');
        }

        // For full updates, validate more fields
        if ($request->has('product_variant_id')) {
            $request->validate([
                'product_variant_id' => 'required|exists:product_variants,id',
                'returned_quantity' => 'required|integer|min:1',
                'return_price_per_unit' => 'required|numeric|min:0',
            ]);

            // Validate that the returned quantity doesn't exceed the originally sold quantity
            $sale = Sale::findOrFail($request->sale_id);
            $saleItem = $sale->items()->where('product_variant_id', $request->product_variant_id)->first();

            if (!$saleItem) {
                return back()->withErrors(['product_variant_id' => 'This product was not sold in the selected sale.']);
            }

            $alreadyReturned = SaleReturn::where('sale_id', $request->sale_id)
                ->where('product_variant_id', $request->product_variant_id)
                ->where('id', '!=', $id)
                ->sum('returned_quantity');

            if (($alreadyReturned + $request->returned_quantity) > $saleItem->quantity) {
                return back()->withErrors(['returned_quantity' => 'Cannot return more than originally sold quantity.']);
            }

            $totalReturnAmount = $request->returned_quantity * $request->return_price_per_unit;

            $saleReturn->update([
                'sale_id' => $request->sale_id,
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
        }

        return redirect()->route('sale-returns.index')
            ->with('success', 'Sale return updated successfully.');
    }

    public function destroy($id)
    {
        $saleReturn = SaleReturn::findOrFail($id);
        $saleReturn->delete();
        return redirect()->route('sale-returns.index')
            ->with('success', 'Sale return deleted successfully.');
    }

    /**
     * Fetch items for a specific sale
     */
    public function getSaleItems($saleId)
    {
        Log::info("getSaleItems called with saleId: " . $saleId);
        $sale = Sale::with(['items.productVariant.product', 'customer'])->findOrFail($saleId);

        $totalReturnAmount = 0;
        $items = $sale->items->map(function($item) use (&$totalReturnAmount) {
            // Check for already returned quantities
            $alreadyReturned = SaleReturn::where('sale_id', $item->sale_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->sum('returned_quantity');

            $remainingQuantity = $item->quantity - $alreadyReturned;

            // Calculate the subtotal for this item
            $subtotal = 0;
            if ($remainingQuantity > 0) {
                $subtotal = $remainingQuantity * $item->price_per_unit;
                $totalReturnAmount += $subtotal;
            }

            return [
                'id' => $item->id,
                'product_name' => $item->productVariant->product->name . ' ' .
                                  ($item->productVariant->variant_name ?? ''),
                'quantity' => $item->quantity,
                'already_returned' => $alreadyReturned,
                'remaining' => $remainingQuantity,
                'price_per_unit' => $item->price_per_unit,
                'total_price' => $item->total_price,
                'subtotal' => $subtotal, // Add subtotal to the response
            ];
        });

        $response = [
            'sale' => $sale,
            'items' => $items,
            'total_return_amount' => $totalReturnAmount // Use our calculated total
        ];

        Log::info("getSaleItems response prepared with " . count($items) . " items and total: " . $totalReturnAmount);

        return response()->json($response);
    }
}
