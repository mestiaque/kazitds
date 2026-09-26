<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Sale;
use ME\Kazitds\Models\Customer;
use ME\Kazitds\Models\LowStock;
use ME\Kazitds\Models\Purchase;
use ME\Kazitds\Models\SaleReturn;
use ME\Kazitds\Models\PurchaseReturn;
use ME\Kazitds\Models\PaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $lowStockThreshold = LowStock::getThreshold();

        $today = today();
        $monthStart = now()->startOfMonth();
        // Same number of days of last month, so the comparison is fair mid-month
        $lastMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthToDate = $lastMonthStart->copy()->addDays($today->day - 1)->endOfDay()
            ->min($lastMonthStart->copy()->endOfMonth());

        // ---- Today ----
        $salesToday = (float) Sale::whereDate('sale_date', $today)->sum('net_amount');
        $salesYesterday = (float) Sale::whereDate('sale_date', $today->copy()->subDay())->sum('net_amount');
        $invoicesToday = Sale::whereDate('sale_date', $today)->count();
        $collectionToday = (float) PaymentHistory::whereDate('created_at', $today)->sum('amount');
        $purchasesToday = (float) Purchase::whereDate('purchase_date', $today)->sum('total_price');

        // ---- This month ----
        $salesMonth = (float) Sale::whereBetween('sale_date', [$monthStart, now()])->sum('net_amount');
        $salesLastMonthToDate = (float) Sale::whereBetween('sale_date', [$lastMonthStart, $lastMonthToDate])->sum('net_amount');
        $invoicesMonth = Sale::whereBetween('sale_date', [$monthStart, now()])->count();
        $purchasesMonth = (float) Purchase::whereBetween('purchase_date', [$monthStart->toDateString(), $today->toDateString()])->sum('total_price');

        // ---- Stock (grouped queries instead of 4 queries per variant) ----
        $stock = $this->stockByVariant();
        $avgCost = $stock->pluck('avg_cost', 'id');
        $lowStockItems = $stock->filter(fn ($v) => $v->current_stock <= $lowStockThreshold)->sortBy('current_stock');

        // ---- Estimated gross profit this month: net sales - cost at avg purchase price, net of approved returns ----
        $costOfSalesMonth = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.sale_date', [$monthStart, now()])
            ->select('sale_items.product_variant_id', DB::raw('SUM(sale_items.quantity) as qty'))
            ->groupBy('sale_items.product_variant_id')
            ->get()
            ->sum(fn ($row) => $row->qty * ($avgCost[$row->product_variant_id] ?? 0));

        $returnsMonth = SaleReturn::where('status', 'approved')
            ->whereBetween('return_date', [$monthStart->toDateString(), $today->toDateString()])
            ->get(['product_variant_id', 'returned_quantity', 'total_return_amount']);
        $returnCostMonth = $returnsMonth->sum(fn ($r) => $r->returned_quantity * ($avgCost[$r->product_variant_id] ?? 0));

        $netRevenueMonth = $salesMonth - (float) $returnsMonth->sum('total_return_amount');
        $grossProfitMonth = $netRevenueMonth - ($costOfSalesMonth - $returnCostMonth);

        // ---- Top products this month by revenue ----
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('product_variants', 'sale_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$monthStart, now()])
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_qty'), DB::raw('SUM(sale_items.total_price) as total_amount'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_amount')
            ->limit(6)
            ->get();

        $dueCustomers = Customer::where('due_amount', '>', 0);

        return view('kazitds::dashboard', [
            'lowStockThreshold' => $lowStockThreshold,

            'salesToday' => $salesToday,
            'salesChangeToday' => $this->percentChange($salesToday, $salesYesterday),
            'invoicesToday' => $invoicesToday,
            'collectionToday' => $collectionToday,
            'purchasesToday' => $purchasesToday,

            'salesMonth' => $salesMonth,
            'salesChangeMonth' => $this->percentChange($salesMonth, $salesLastMonthToDate),
            'invoicesMonth' => $invoicesMonth,
            'averageSaleMonth' => $invoicesMonth > 0 ? $salesMonth / $invoicesMonth : 0,
            'purchasesMonth' => $purchasesMonth,
            'grossProfitMonth' => $grossProfitMonth,
            'grossMargin' => $netRevenueMonth > 0 ? ($grossProfitMonth / $netRevenueMonth) * 100 : null,

            'stockValue' => $stock->sum(fn ($v) => max($v->current_stock, 0) * $v->avg_cost),
            'stockUnits' => $stock->sum(fn ($v) => max($v->current_stock, 0)),
            'lowStockCount' => $lowStockItems->count(),
            'lowStockItems' => $lowStockItems->take(6),
            'outOfStockCount' => $stock->filter(fn ($v) => $v->current_stock <= 0)->count(),

            'totalDue' => (float) (clone $dueCustomers)->sum('due_amount'),
            'dueCustomerCount' => (clone $dueCustomers)->count(),
            'topDueCustomers' => (clone $dueCustomers)->orderByDesc('due_amount')->take(6)->get(),

            'topProducts' => $topProducts,
            'pendingSaleReturns' => SaleReturn::where('status', 'pending')->count(),
            'pendingPurchaseReturns' => PurchaseReturn::where('status', 'pending')->count(),
            'monthlySales' => $this->monthlySales(12),

            'recentSales' => Sale::with('customer')->latest('sale_date')->latest('id')->take(6)->get(),
            'recentPurchases' => Purchase::with(['productVariant.product', 'supplier'])->latest('purchase_date')->latest('id')->take(6)->get(),
        ]);
    }

    /**
     * Daily sales and purchases for the trend chart (7, 30 or 90 days).
     */
    public function salesChartData(Request $request)
    {
        $days = in_array((int) $request->query('days'), [7, 30, 90]) ? (int) $request->query('days') : 30;
        $start = today()->subDays($days - 1);

        $sales = Sale::where('sale_date', '>=', $start)
            ->selectRaw('DATE(sale_date) as d, SUM(net_amount) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        $purchases = Purchase::where('purchase_date', '>=', $start->toDateString())
            ->selectRaw('DATE(purchase_date) as d, SUM(total_price) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        $labels = $salesData = $purchaseData = [];
        for ($date = $start->copy(); $date->lte(today()); $date->addDay()) {
            $key = $date->toDateString();
            $labels[] = toBanglaPhone($date->translatedFormat('d M'));
            $salesData[] = round((float) ($sales[$key] ?? 0), 2);
            $purchaseData[] = round((float) ($purchases[$key] ?? 0), 2);
        }

        return response()->json([
            'labels' => $labels,
            'sales' => $salesData,
            'purchases' => $purchaseData,
        ]);
    }

    /**
     * Current stock and weighted average purchase cost of every variant.
     * Same formula as ProductVariant::getCurrentStock():
     * purchased - sold + approved sale returns - approved purchase returns.
     */
    private function stockByVariant(): Collection
    {
        $purchased = DB::table('purchases')
            ->select('product_variant_id', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(total_price) as cost'))
            ->groupBy('product_variant_id');
        $sold = DB::table('sale_items')
            ->select('product_variant_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('product_variant_id');
        $saleReturned = DB::table('sale_returns')->where('status', 'approved')
            ->select('product_variant_id', DB::raw('SUM(returned_quantity) as qty'))
            ->groupBy('product_variant_id');
        $purchaseReturned = DB::table('purchase_returns')->where('status', 'approved')
            ->select('product_variant_id', DB::raw('SUM(returned_quantity) as qty'))
            ->groupBy('product_variant_id');

        return DB::table('product_variants as pv')
            ->join('products', 'products.id', '=', 'pv.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'pv.brand_id')
            ->leftJoin('packs', 'packs.id', '=', 'pv.pack_id')
            ->leftJoinSub($purchased, 'p', 'p.product_variant_id', '=', 'pv.id')
            ->leftJoinSub($sold, 's', 's.product_variant_id', '=', 'pv.id')
            ->leftJoinSub($saleReturned, 'sr', 'sr.product_variant_id', '=', 'pv.id')
            ->leftJoinSub($purchaseReturned, 'pr', 'pr.product_variant_id', '=', 'pv.id')
            ->select(
                'pv.id',
                'products.name as product_name',
                'brands.name as brand_name',
                'packs.name as pack_name',
                DB::raw('COALESCE(p.qty, 0) - COALESCE(s.qty, 0) + COALESCE(sr.qty, 0) - COALESCE(pr.qty, 0) as current_stock'),
                'p.qty as purchased_qty',
                'p.cost as purchased_cost'
            )
            ->get()
            ->map(function ($v) {
                $v->current_stock = (int) $v->current_stock;
                $v->avg_cost = $v->purchased_qty > 0 ? $v->purchased_cost / $v->purchased_qty : 0;
                return $v;
            });
    }

    private function monthlySales(int $months): array
    {
        $start = now()->subMonthsNoOverflow($months - 1)->startOfMonth();

        $totals = Sale::where('sale_date', '>=', $start)
            ->selectRaw("DATE_FORMAT(sale_date, '%Y-%m') as m, SUM(net_amount) as total")
            ->groupBy('m')
            ->pluck('total', 'm');

        $labels = $data = [];
        for ($month = $start->copy(); $month->lte(now()); $month->addMonthNoOverflow()) {
            $labels[] = toBanglaPhone($month->translatedFormat('M y'));
            $data[] = round((float) ($totals[$month->format('Y-m')] ?? 0), 2);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function percentChange(float $current, float $previous): ?float
    {
        return $previous > 0 ? (($current - $previous) / $previous) * 100 : null;
    }
}
