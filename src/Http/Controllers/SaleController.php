<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Http\Services\SmsNotifier;
use ME\Kazitds\Models\Customer;
use ME\Kazitds\Models\Due;
use ME\Kazitds\Models\PaymentHistory;
use ME\Kazitds\Models\ProductVariant;
use ME\Kazitds\Models\Sale;
use ME\Kazitds\Models\SaleItem;
use ME\Kazitds\Models\Setting;
use ME\Kazitds\Http\Services\CustomerSaleLedgerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{

    public function __construct()
    {
        $this->middleware('authorization:sale.view')->only(['index', 'show']);
        $this->middleware('authorization:sale.create')->only(['create', 'store']);
        $this->middleware('authorization:sale.edit')->only(['edit', 'update']);
        $this->middleware('authorization:sale.delete')->only('destroy');
        $this->middleware('authorization:sale.print')->only('printInvoice');
    }

    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'items.productVariant.product']);

        if ($request->has('invoice_number') && $request->input('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->input('invoice_number') . '%');
        }

        if ($request->has('customer_name') && $request->input('customer_name')) {
            $query->where('customer_name', 'like', '%' . $request->input('customer_name') . '%');
        }

        if ($request->has('start_date') && $request->input('start_date')) {
            $query->whereDate('sale_date', '>=', $request->input('start_date'));
        }

        if ($request->has('end_date') && $request->input('end_date')) {
            $query->whereDate('sale_date', '<=', $request->input('end_date'));
        }

        $sales = $query->orderBy('id', 'desc')->paginate(get_setting('pagination', 10));

        return view('kazitds::sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::all();
        $productVariants = ProductVariant::with(['product', 'brand', 'pack'])->where('is_active', true)->get();

        // Generate next invoice number
        $lastSale = Sale::latest()->first();
        $nextInvoiceNumberInt = 1;
        if ($lastSale && preg_match('/INV-(\d+)/', $lastSale->invoice_number, $matches)) {
            $lastInvoiceNumberInt = intval($matches[1]);
            $nextInvoiceNumberInt = $lastInvoiceNumberInt + 1;
        }
        $invoiceNumber = 'INV-' . str_pad($nextInvoiceNumberInt, 8, '0', STR_PAD_LEFT);

        // Add stock quantity to each product variant
        foreach ($productVariants as $variant) {
            $variant->stock = $variant->getCurrentStock();
        }

        // Get form display settings as boolean values
        $showDiscount = (bool) Setting::get('show_discount_option', true);
        $showPreviousDue = (bool) Setting::get('show_previous_due_option', true);

        return view('kazitds::sales.create', compact('customers', 'productVariants', 'invoiceNumber', 'showDiscount', 'showPreviousDue'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'sale_date' => 'required',
            'discount' => 'nullable|numeric|min:0',
            'previous_due' => 'nullable|numeric|min:0',
            'payment' => 'nullable|numeric|min:0',
            'due_after_payment' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_per_unit' => 'required|numeric|min:0',
            'items.*.item_discount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $lastSale = Sale::orderBy('id', 'desc')->first();
            $nextInvoiceNumberInt = 1;
            if ($lastSale && preg_match('/INV-(\d+)/', $lastSale->invoice_number, $matches)) {
                $lastInvoiceNumberInt = intval($matches[1]);
                $nextInvoiceNumberInt = $lastInvoiceNumberInt + 1;
            }
            $invoiceNumber = 'INV-' . str_pad($nextInvoiceNumberInt, 8, '0', STR_PAD_LEFT);

            // Handle customer
            $customerId = $request->customer_id;
            $customerName = $request->customer_name;
            $mobileNumber = $request->mobile_number;

            $customer = null;
            $saleDate = Carbon::parse($request->sale_date)->setTimeFrom(now());

            if (!$customerId) {

                // phone দিয়ে খোঁজা
                if ($mobileNumber) {
                    $customer = Customer::where('phone', $mobileNumber)->first();
                }

                // phone না পেলে name দিয়ে খোঁজা
                if (!$customer && $customerName) {
                    $customer = Customer::where('name', $customerName)->first();
                }

                // কিছুই না পেলে create
                if (!$customer && ($customerName || $mobileNumber)) {
                    $customer = Customer::create([
                        'name' => $customerName,
                        'phone' => $mobileNumber,
                    ]);
                }

                if ($customer) {
                    $customerId = $customer->id;
                    $customerName = $customer->name;
                    $mobileNumber = $customer->phone;
                }
            }

            // If customer_id was given, fetch customer
            if ($customerId && empty($customer)) {
                $customer = Customer::find($customerId);
            }

            if (!$customer) {
                throw new \Exception(__('kazitds::kazitds.Please select or create a customer before creating a sale.'));
            }

            $hasPriorSales = Sale::where('customer_id', $customer->id)->exists();
            $openingPreviousDue = $hasPriorSales ? 0 : (float) ($customer->due_amount ?? 0);

            // Create the sale
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customer->id,
                'customer_name' => $customerName ?? $customer?->name,
                'mobile_number' => $mobileNumber ?? $customer?->phone,
                'sale_date' => $saleDate,
                'discount' => $request->discount ?? 0,
                'previous_due' => $openingPreviousDue,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // Add items
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $variant = ProductVariant::findOrFail($item['product_variant_id']);
                $currentStock = $variant->getCurrentStock();

                if ($currentStock < $item['quantity']) {
                    throw new \Exception(__('kazitds::kazitds.not_enough_stock', [
                        'productName' => $variant->product->name,
                        'currentStock' => $currentStock,
                        'requestedQty' => $item['quantity'],
                    ]));
                }

                $itemDiscount = $item['item_discount'] ?? 0;
                $itemTotal = ($item['quantity'] * $item['price_per_unit']) - $itemDiscount;
                $totalAmount += $itemTotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'price_per_unit' => $item['price_per_unit'],
                    'item_discount' => $itemDiscount,
                    'total_price' => $itemTotal
                ]);
            }

            // Update sale totals
            $sale->total_amount = $totalAmount;
            $sale->net_amount = $totalAmount - $sale->discount ;
            $sale->save();

            // ====== Add PaymentHistory record ======
            if (!empty($request->payment) && $request->payment > 0) {
                PaymentHistory::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $customer->id,
                    'amount' => $request->payment,
                    'payment_method' => $request->payment_method ?? 'Cash',
                    'note' => $request->notes,
                ]);
            }

            app(CustomerSaleLedgerService::class)->recalculateForCustomer($customer->id);
            $dueAmount = (float) (Due::where('sale_id', $sale->id)->value('total_due') ?? 0);


            DB::commit();

            if($customer && $customer->phone){
                if (isset(get_setting('sms_permit')['create']) && get_setting('sms_permit')['create'] == 1) {
                    if ($dueAmount > 0 && $request->input('sms')) {
                        SmsNotifier::newSaleCreate($customer, $totalAmount, $request->payment ?? 0, $dueAmount);
                    }
                }
            }

            return redirect()->route('sales.show', $sale->id)
                ->with('success', __('kazitds::kazitds.sale_created_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function show(Sale $sale)
    {
        $sale->load('customer', 'items.productVariant.product', 'items.productVariant.brand', 'items.productVariant.pack');
        $dues = Due::where('sale_id', $sale->id)->first();
        return view('kazitds::sales.show', compact('sale', 'dues'));
    }

    public function edit(Sale $sale)
    {
        // Check if the sale has any returns
        if ($sale->returns()->exists()) {
            return redirect()->route('sales.show', $sale->id)
                ->with('error', 'This sale cannot be edited because it has returns associated with it.');
        }

        $sale->load('customer', 'items.productVariant.product', 'items.productVariant.brand', 'items.productVariant.pack');

        $customers = Customer::all();
        $productVariants = ProductVariant::with(['product', 'brand', 'pack'])->where('is_active', true)->get();
        $dues = Due::where('sale_id', $sale->id)->first();

        // Add stock quantity to each product variant (plus quantities in this sale)
        foreach ($productVariants as $variant) {
            $saleItem = $sale->items->where('product_variant_id', $variant->id)->first();
            $currentQuantity = $saleItem ? $saleItem->quantity : 0;
            $variant->stock = $variant->getCurrentStock() + $currentQuantity;
        }

        // Get form display settings as boolean values
        $showDiscount = (bool) Setting::get('show_discount_option', true);
        $showPreviousDue = (bool) Setting::get('show_previous_due_option', true);

        return view('kazitds::sales.edit', compact('sale', 'customers', 'productVariants', 'showDiscount', 'showPreviousDue', 'dues'));
    }

    public function update(Request $request, Sale $sale)
    {
        try {
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'customer_name' => 'nullable|string|max:255',
                'mobile_number' => 'nullable|string|max:20',
                'sale_date' => 'required',
                'discount' => 'nullable|numeric|min:0',
                'previous_due' => 'nullable|numeric|min:0',
                'payment' => 'nullable|numeric|min:0',
                'due_after_payment' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.id' => 'nullable|exists:sale_items,id',
                'items.*.product_variant_id' => 'required|exists:product_variants,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price_per_unit' => 'required|numeric|min:0',
                'items.*.item_discount' => 'nullable|numeric|min:0',
            ]);

            DB::beginTransaction();

            $oldCustomerId = $sale->customer_id;
            $customerId = (int) $request->input('customer_id');
            $customer = Customer::find($customerId);

            if (!$customer) {
                throw new \Exception(__('kazitds::kazitds.Selected customer was not found.'));
            }

            $saleDate = Carbon::parse($request->sale_date)->setTimeFrom(now());

            // Update sale
            $sale->update([
                'customer_id'   => $customerId,
                'customer_name' => $customer->name,
                'mobile_number' => $customer->phone,
                'sale_date'     => $saleDate,
                'discount'      => $request->discount ?? 0,
                'notes'         => $request->notes,
                'updated_by'    => auth()->id(),
            ]);

            // Process items
            $currentItems = $sale->items->keyBy('id');
            $updatedItemIds = [];
            $totalAmount = 0;

            foreach ($request->items as $itemData) {
                $variant = ProductVariant::findOrFail($itemData['product_variant_id']);
                $currentStock = $variant->getCurrentStock();

                // Check for stock only on new items or increased quantity
                $existingItem = isset($itemData['id']) ? $currentItems->get($itemData['id']) : null;
                $requestedQty = $itemData['quantity'];
                $oldQty = $existingItem ? $existingItem->quantity : 0;
                $isVariantChanged = $existingItem && ((int) $existingItem->product_variant_id !== (int) $itemData['product_variant_id']);
                $requiredQty = $isVariantChanged ? $requestedQty : max(0, $requestedQty - $oldQty);

                if ($requiredQty > 0 && $currentStock < $requiredQty) {
                    throw new \Exception(__('kazitds::kazitds.not_enough_stock', [
                        'productName' => $variant->product->name,
                        'currentStock' => $currentStock,
                        'requestedQty' => $requiredQty,
                    ]));
                } elseif (!$existingItem && $currentStock < $requestedQty) {
                    throw new \Exception(__('kazitds::kazitds.not_enough_stock', [
                        'productName' => $variant->product->name,
                        'currentStock' => $currentStock,
                        'requestedQty' => $requestedQty,
                    ]));
                }

                $itemDiscount = $itemData['item_discount'] ?? 0;
                $itemTotal = ($requestedQty * $itemData['price_per_unit']) - $itemDiscount;
                $totalAmount += $itemTotal;

                if ($existingItem) {
                    $existingItem->update([
                        'product_variant_id' => $itemData['product_variant_id'],
                        'quantity'           => $requestedQty,
                        'price_per_unit'     => $itemData['price_per_unit'],
                        'item_discount'      => $itemDiscount,
                        'total_price'        => $itemTotal,
                    ]);
                    $updatedItemIds[] = $existingItem->id;
                } else {
                    $newItem = SaleItem::create([
                        'sale_id'            => $sale->id,
                        'product_variant_id' => $itemData['product_variant_id'],
                        'quantity'           => $requestedQty,
                        'price_per_unit'     => $itemData['price_per_unit'],
                        'item_discount'      => $itemDiscount,
                        'total_price'        => $itemTotal,
                    ]);
                    $updatedItemIds[] = $newItem->id;
                }
            }

            // Remove deleted items
            foreach ($currentItems as $id => $item) {
                if (!in_array($id, $updatedItemIds)) {
                    $item->delete();
                }
            }

            // Update sale totals
            $sale->total_amount = $totalAmount;
            $sale->net_amount = $totalAmount - $sale->discount;
            $sale->save();

            // Update or create PaymentHistory if payment exists
            if (!empty($request->payment) && $request->payment > 0) {
                // Keep a single, normalized payment row per sale for stable ledger recalculation.
                PaymentHistory::where('sale_id', $sale->id)->delete();
                PaymentHistory::create([
                    'sale_id'         => $sale->id,
                    'customer_id'     => $customerId,
                    'amount'          => $request->payment,
                    'payment_method'  => $request->payment_method ?? 'Cash',
                    'note'            => $request->notes,
                ]);
            } else {
                PaymentHistory::where('sale_id', $sale->id)->delete();
            }

            app(CustomerSaleLedgerService::class)->recalculateForCustomer($customerId);
            if ($oldCustomerId && $oldCustomerId !== $customerId) {
                app(CustomerSaleLedgerService::class)->recalculateForCustomer($oldCustomerId);
            }
            $dueAmount = (float) (Due::where('sale_id', $sale->id)->value('total_due') ?? 0);

            DB::commit();

            if (isset(get_setting('sms_permit')['edit']) && get_setting('sms_permit')['edit'] == 1) {
                if ($dueAmount > 0 && $request->input('sms')) {
                    SmsNotifier::newSaleCreate($customer, $totalAmount, $request->payment ?? 0, $dueAmount);
                }
            }

            return redirect()->route('sales.show', $sale->id)
                ->with('success', __('kazitds::kazitds.sale_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }


    public function destroy(Sale $sale)
    {
        // Check if the sale has any returns
        if ($sale->returns()->exists()) {
            return redirect()->route('sales.show', $sale->id)
                ->with('error', __('kazitds::kazitds.This sale cannot be deleted because it has returns associated with it.'));
        }

        try {
            DB::beginTransaction();
            $customerId = $sale->customer_id;

            // Delete all sale items
            $sale->items()->delete();

            // Delete PaymentHistory records for this sale (Due will be handled by recalculateForCustomer)
            \ME\Kazitds\Models\PaymentHistory::where('sale_id', $sale->id)->delete();

            // Delete the sale (Due records cascade delete via foreign key)
            $sale->delete();

            if ($customerId) {
                // This will recalculate all dues for remaining sales and clean up stale due records
                app(CustomerSaleLedgerService::class)->recalculateForCustomer($customerId);
            }

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', __('kazitds::kazitds.Sale deleted successfully. All items have been returned to stock.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function getProductVariant($id)
    {
        $variant = ProductVariant::with(['product', 'brand', 'pack'])
            ->findOrFail($id);

        $variant->stock = $variant->getCurrentStock();

        return response()->json($variant);
    }

    public function printInvoice(Sale $sale)
    {
        $sale->load('customer', 'items.productVariant.product', 'items.productVariant.brand', 'items.productVariant.pack');
        $dues = Due::where('sale_id', $sale->id)->first();
        return view('kazitds::sales.invoice', compact('sale', 'dues'));
    }
}
