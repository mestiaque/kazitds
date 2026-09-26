<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use Carbon\Carbon;
use ME\Kazitds\Models\Sale;
use ME\Kazitds\Models\Brand;
use ME\Kazitds\Models\Customer;
use ME\Kazitds\Models\Supplier;
use ME\Kazitds\Models\Product;
use ME\Kazitds\Models\LowStock;
use ME\Kazitds\Models\Purchase;
use ME\Kazitds\Models\SaleItem;
use Illuminate\Http\Request;
use ME\Kazitds\Models\ProductVariant;
use ME\Models\SmsAccount;
use ME\Models\SmsLog;

class ReportController extends Controller
{

    public function __construct()
    {
        $this->middleware('authorization:stock_report.view')->only(['stock']);
        $this->middleware('authorization:stock_report.print')->only(['printStockReport']);
        $this->middleware('authorization:sale_report.view')->only(['sales']);
        $this->middleware('authorization:sale_report.print')->only(['printSalesReport']);
        $this->middleware('authorization:purchase_report.view')->only(['purchases']);
        $this->middleware('authorization:purchase_report.print')->only(['printPurchasesReport']);
        $this->middleware('authorization:low_stock.view')->only(['low_stock']);
        $this->middleware('authorization:sale_report.view')->only(['productVariantSales']);
        $this->middleware('authorization:sale_report.print')->only(['printProductVariantSalesReport']);
        $this->middleware('authorization:sms_report.view')->only(['smsLog']);
    }

    public function stock(Request $request)
    {
        $query = ProductVariant::with(['product', 'brand', 'pack']);

        if ($request->has('product') && $request->product) {
            $query->where('product_id', $request->product);
        }

        if ($request->has('brand') && $request->brand) {
            $query->where('brand_id', $request->brand);
        }

        $productVariants = $query->get();

        // Calculate stock for each variant
        $stockReport = $productVariants->map(function ($variant) {
            $variant->current_stock = $variant->getCurrentStock();

            // Get average purchase price
            $avgPurchasePrice = $variant->purchases()->avg('price_per_unit') ?? 0;
            $variant->avg_purchase_price = $avgPurchasePrice;
            $variant->stock_value = $variant->current_stock * $avgPurchasePrice;

            return $variant;
        });

        // Filter out items with zero stock if requested
        if ($request->has('show_zero_stock') && $request->show_zero_stock == '0') {
            $stockReport = $stockReport->filter(function ($variant) {
                return $variant->current_stock > 0;
            });
        }

        // Calculate totals
        $totalStockValue = $stockReport->sum('stock_value');
        $totalItems = $stockReport->sum('current_stock');

        // Get products and brands for filters
        $products = Product::all();
        $brands = Brand::all();

        return view('kazitds::reports.stock', compact('stockReport', 'products', 'brands', 'totalStockValue', 'totalItems'));
    }

    public function printStockReport(Request $request)
    {
        // Same logic as stock method but return a different view for printing
        $query = ProductVariant::with(['product', 'brand', 'pack']);

        if ($request->has('product') && $request->product) {
            $query->where('product_id', $request->product);
        }

        if ($request->has('brand') && $request->brand) {
            $query->where('brand_id', $request->brand);
        }

        $productVariants = $query->get();

        // Calculate stock for each variant
        $stockReport = $productVariants->map(function ($variant) {
            $variant->current_stock = $variant->getCurrentStock();

            // Get average purchase price
            $avgPurchasePrice = $variant->purchases()->avg('price_per_unit') ?? 0;
            $variant->avg_purchase_price = $avgPurchasePrice;
            $variant->stock_value = $variant->current_stock * $avgPurchasePrice;

            return $variant;
        });

        // Filter out items with zero stock if requested
        if ($request->has('show_zero_stock') && $request->show_zero_stock == '0') {
            $stockReport = $stockReport->filter(function ($variant) {
                return $variant->current_stock > 0;
            });
        }

        // Calculate totals
        $totalStockValue = $stockReport->sum('stock_value');
        $totalItems = $stockReport->sum('current_stock');

        return view('kazitds::reports.print.stock', compact('stockReport', 'totalStockValue', 'totalItems'));
    }

    // Sales report: product-wise (default) or invoice-wise (?view=list)
    public function sales(Request $request)
    {
        return view('kazitds::reports.sales', $this->salesReportData($request, 20) + [
            'customers'       => Customer::orderBy('name')->get(),
            'productVariants' => ProductVariant::with('product', 'brand', 'pack')->get(),
        ]);
    }

    public function printSalesReport(Request $request)
    {
        return view('kazitds::reports.print.sales', $this->salesReportData($request) + $this->reportPeriod($request));
    }

    // Purchase report: product-wise (default) or purchase-wise (?view=list)
    public function purchases(Request $request)
    {
        return view('kazitds::reports.purchases', $this->purchaseReportData($request, 20) + [
            'suppliers'       => Supplier::orderBy('name')->get(),
            'productVariants' => ProductVariant::with('product', 'brand', 'pack')->get(),
        ]);
    }

    public function printPurchasesReport(Request $request)
    {
        return view('kazitds::reports.print.purchases', $this->purchaseReportData($request) + $this->reportPeriod($request) + [
            'supplierName' => $request->supplier_id ? Supplier::find($request->supplier_id)?->name : null,
        ]);
    }

    /**
     * Rows and grand totals for the sales report. Totals always cover the whole filtered range,
     * not just the current page. $perPage = null returns every row (print).
     */
    private function salesReportData(Request $request, ?int $perPage = null): array
    {
        $view = $request->input('view') === 'list' ? 'list' : 'product';
        $sales = $this->salesQuery($request);

        $totals = (clone $sales)->toBase()
            ->selectRaw('COUNT(*) as invoices, COALESCE(SUM(total_amount), 0) as subtotal, COALESCE(SUM(discount), 0) as discount, COALESCE(SUM(net_amount), 0) as net')
            ->first();

        $items = SaleItem::query()
            ->whereIn('sale_id', (clone $sales)->select('sales.id'))
            ->when($request->product_variant_id, fn ($q, $id) => $q->where('product_variant_id', $id));

        $itemTotals = (clone $items)->toBase()
            ->selectRaw('COALESCE(SUM(quantity), 0) as quantity, COALESCE(SUM(total_price), 0) as amount')
            ->first();

        if ($view === 'product') {
            $rows = $items->select('product_variant_id')
                ->selectRaw('SUM(quantity) as total_quantity, SUM(total_price) as total_amount, COUNT(DISTINCT sale_id) as invoice_count')
                ->groupBy('product_variant_id')
                ->orderByDesc('total_amount');
            $rows = $this->attachVariants($perPage ? $rows->paginate($perPage)->withQueryString() : $rows->get());
        } else {
            $rows = (clone $sales)->with(['items', 'customer'])->orderByDesc('sale_date')->orderByDesc('id');
            $rows = $perPage ? $rows->paginate($perPage)->withQueryString() : $rows->get();
        }

        return compact('view', 'rows', 'totals', 'itemTotals');
    }

    private function purchaseReportData(Request $request, ?int $perPage = null): array
    {
        $view = $request->input('view') === 'list' ? 'list' : 'product';
        $purchases = $this->purchasesQuery($request);

        $totals = (clone $purchases)->toBase()
            ->selectRaw('COUNT(*) as purchases, COALESCE(SUM(quantity), 0) as quantity, COALESCE(SUM(total_price), 0) as amount')
            ->first();

        if ($view === 'product') {
            $rows = (clone $purchases)->select('product_variant_id')
                ->selectRaw('SUM(quantity) as total_quantity, SUM(total_price) as total_amount, COUNT(*) as purchase_count')
                ->groupBy('product_variant_id')
                ->orderByDesc('total_amount');
            $rows = $this->attachVariants($perPage ? $rows->paginate($perPage)->withQueryString() : $rows->get());
        } else {
            $rows = (clone $purchases)->with(['productVariant.product', 'productVariant.brand', 'productVariant.pack', 'supplier'])
                ->orderByDesc('purchase_date')->orderByDesc('id');
            $rows = $perPage ? $rows->paginate($perPage)->withQueryString() : $rows->get();
        }

        return compact('view', 'rows', 'totals');
    }

    private function salesQuery(Request $request)
    {
        return Sale::query()
            ->whereDoesntHave('returns') // sales with returns are left out of this report
            ->when($request->start_date, fn ($q, $date) => $q->whereDate('sale_date', '>=', $date))
            ->when($request->end_date, fn ($q, $date) => $q->whereDate('sale_date', '<=', $date))
            ->when($request->customer_id, fn ($q, $id) => $q->where('customer_id', $id))
            ->when($request->invoice_number, fn ($q, $number) => $q->where('invoice_number', 'like', "%{$number}%"))
            ->when($request->product_variant_id, fn ($q, $id) => $q->whereHas('items', fn ($items) => $items->where('product_variant_id', $id)));
    }

    private function purchasesQuery(Request $request)
    {
        return Purchase::query()
            ->when($request->start_date, fn ($q, $date) => $q->whereDate('purchase_date', '>=', $date))
            ->when($request->end_date, fn ($q, $date) => $q->whereDate('purchase_date', '<=', $date))
            ->when($request->supplier_id, fn ($q, $id) => $q->where('supplier_id', $id))
            ->when($request->product_variant_id, fn ($q, $id) => $q->where('product_variant_id', $id));
    }

    /**
     * Give each grouped row its product variant (product, brand, pack) and weighted average price.
     */
    private function attachVariants($rows)
    {
        $variants = ProductVariant::with('product', 'brand', 'pack')
            ->whereIn('id', collect($rows instanceof \Illuminate\Contracts\Pagination\Paginator ? $rows->items() : $rows)->pluck('product_variant_id'))
            ->get()
            ->keyBy('id');

        foreach ($rows as $row) {
            $row->variant = $variants[$row->product_variant_id] ?? null;
            $row->avg_price = $row->total_quantity > 0 ? $row->total_amount / $row->total_quantity : 0;
        }

        return $rows;
    }

    private function reportPeriod(Request $request): array
    {
        return [
            'startDate' => $request->start_date ? formatDate($request->start_date) : __('kazitds::kazitds.Start time'),
            'endDate'   => $request->end_date ? formatDate($request->end_date) : __('kazitds::kazitds.Present'),
        ];
    }

    // Product Variant Wise Sales Report
    public function productVariantSales(Request $request)
    {
        $query = SaleItem::with(['productVariant.product', 'productVariant.brand', 'productVariant.pack', 'sale'])
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereDoesntHave('sale.returns');

        // Apply date filters
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('sales.sale_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('sales.sale_date', '<=', $request->end_date);
        }

        // Apply product variant filter
        if ($request->has('product_variant_id') && $request->product_variant_id) {
            $query->where('product_variant_id', $request->product_variant_id);
        }

        // Group by product_variant_id and calculate totals
        $productVariantSales = $query->select('sale_items.product_variant_id')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity')
            ->selectRaw('SUM(sale_items.total_price) as total_amount')
            ->selectRaw('AVG(sale_items.price_per_unit) as avg_price')
            ->groupBy('sale_items.product_variant_id')
            ->orderByDesc('total_quantity')
            ->paginate(20);

        // Load the productVariant relationship for the paginated results
        $productVariantIds = $productVariantSales->pluck('product_variant_id');
        $productVariants = ProductVariant::with(['product', 'brand', 'pack'])
            ->whereIn('id', $productVariantIds)
            ->get()
            ->keyBy('id');

        // Calculate totals
        $totalQuantity = $productVariantSales->sum('total_quantity');
        $totalAmount = $productVariantSales->sum('total_amount');

        // Get product variants for filter
        $productVariantsFilter = ProductVariant::with('product', 'brand', 'pack')->get();

        return view('kazitds::reports.product-variant-sales',
            compact('productVariantSales', 'productVariants', 'productVariantsFilter', 'totalQuantity', 'totalAmount'));
    }

    public function printProductVariantSalesReport(Request $request)
    {
        $query = SaleItem::with(['productVariant.product', 'productVariant.brand', 'productVariant.pack', 'sale'])
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereDoesntHave('sale.returns');

        // Apply date filters
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('sales.sale_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('sales.sale_date', '<=', $request->end_date);
        }

        // Apply product variant filter
        if ($request->has('product_variant_id') && $request->product_variant_id) {
            $query->where('product_variant_id', $request->product_variant_id);
        }

        // Group by product_variant_id and calculate totals
        $productVariantSales = $query->select('sale_items.product_variant_id')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity')
            ->selectRaw('SUM(sale_items.total_price) as total_amount')
            ->selectRaw('AVG(sale_items.price_per_unit) as avg_price')
            ->groupBy('sale_items.product_variant_id')
            ->orderByDesc('total_quantity')
            ->get();

        // Load the productVariant relationship
        $productVariantIds = $productVariantSales->pluck('product_variant_id');
        $productVariants = ProductVariant::with(['product', 'brand', 'pack'])
            ->whereIn('id', $productVariantIds)
            ->get()
            ->keyBy('id');

        // Calculate totals
        $totalQuantity = $productVariantSales->sum('total_quantity');
        $totalAmount = $productVariantSales->sum('total_amount');

        // Format date range for the report title
        $startDate = $request->start_date ? formatDate($request->start_date) : __('kazitds::kazitds.Start time');
        $endDate = $request->end_date ? formatDate($request->end_date) : __('kazitds::kazitds.Present');

        return view('kazitds::reports.print.product-variant-sales',
            compact('productVariantSales', 'productVariants', 'totalQuantity', 'totalAmount', 'startDate', 'endDate'));
    }

    // SMS log (read-only): balance overview + sent messages. Recharge and gateway details stay on metheme's page.
    public function smsLog(Request $request)
    {
        $logs = SmsLog::query()
            ->when($request->phone, fn ($q, $phone) => $q->where('to', 'like', "%{$phone}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->start_date, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->end_date, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->latest('id')
            ->paginate(get_setting('pagination', 10))
            ->withQueryString();

        return view('kazitds::reports.sms-log', [
            'logs'          => $logs,
            'account'       => SmsAccount::current(),
            'sentThisMonth' => SmsLog::where('status', 'success')->where('created_at', '>=', now()->startOfMonth())->count(),
        ]);
    }

    public function low_stock()
    {
        $lowStockItems = LowStock::list();
        $totalLowStock = $lowStockItems->count();
        return view('kazitds::low_stock.index', compact('lowStockItems', 'totalLowStock'));
    }
}
