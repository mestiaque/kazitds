@extends('me::master')

@section('title', trans('kazitds::kazitds.Dashboard'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('sales.create'),
      'text' => __('kazitds::kazitds.New Sale'),
      'class' => 'btn-encodex-create'
  ])
  @endcomponent
@endpush

@php
    $tk = __('kazitds::kazitds.TK.');

    // Up/down badge for a percentage change; arrow + sign so it never relies on color alone
    $delta = function ($change, $label) {
        if ($change === null) {
            return '<span class="kz-muted">' . e($label) . ': —</span>';
        }
        $up = $change >= 0;
        return '<span class="kz-delta ' . ($up ? 'kz-up' : 'kz-down') . '">'
            . '<i class="fas fa-arrow-' . ($up ? 'up' : 'down') . '"></i> '
            . ($up ? '+' : '') . toBanglaNumber($change, 1) . '%</span> '
            . '<span class="kz-muted">' . e($label) . '</span>';
    };
@endphp

@section('content')
<div class="kz-dash">

    {{-- ================= Today ================= --}}
    <div class="kz-section-title">
        <span>@lang('kazitds::kazitds.Today')</span>
        <span class="kz-muted">{{ formatDate(today()) }}</span>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kz-tile">
                <div class="kz-tile-icon kz-icon-blue"><i class="fas fa-cash-register"></i></div>
                <div class="kz-tile-label">@lang('kazitds::kazitds.Sales Today')</div>
                <div class="kz-tile-value">{{ $tk }} {{ toBanglaNumber($salesToday, 2) }}</div>
                <div class="kz-tile-sub">
                    {!! $delta($salesChangeToday, __('kazitds::kazitds.vs yesterday')) !!}
                    · @lang('kazitds::kazitds.Invoices'): {{ toBanglaNumber($invoicesToday) }}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kz-tile">
                <div class="kz-tile-icon kz-icon-green"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="kz-tile-label">@lang('kazitds::kazitds.Collection Today')</div>
                <div class="kz-tile-value">{{ $tk }} {{ toBanglaNumber($collectionToday, 2) }}</div>
                <div class="kz-tile-sub kz-muted">@lang('kazitds::kazitds.Payments received, including due payments')</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kz-tile">
                <div class="kz-tile-icon kz-icon-orange"><i class="fas fa-shopping-cart"></i></div>
                <div class="kz-tile-label">@lang('kazitds::kazitds.Purchases Today')</div>
                <div class="kz-tile-value">{{ $tk }} {{ toBanglaNumber($purchasesToday, 2) }}</div>
                <div class="kz-tile-sub kz-muted">
                    @lang('kazitds::kazitds.This month'): {{ $tk }} {{ toBanglaNumber($purchasesMonth, 0) }}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('due.index') }}" class="kz-tile kz-tile-link">
                <div class="kz-tile-icon kz-icon-red"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="kz-tile-label">@lang('kazitds::kazitds.Total Due Receivable')</div>
                <div class="kz-tile-value">{{ $tk }} {{ toBanglaNumber($totalDue, 2) }}</div>
                <div class="kz-tile-sub kz-muted">
                    @lang('kazitds::kazitds.Customers with due'): {{ toBanglaNumber($dueCustomerCount) }}
                </div>
            </a>
        </div>
    </div>

    {{-- ================= This month ================= --}}
    <div class="kz-section-title">
        <span>@lang('kazitds::kazitds.This Month')</span>
        <span class="kz-muted">{{ toBanglaPhone(now()->translatedFormat('F Y')) }}</span>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kz-tile">
                <div class="kz-tile-icon kz-icon-blue"><i class="fas fa-chart-line"></i></div>
                <div class="kz-tile-label">@lang('kazitds::kazitds.Sales This Month')</div>
                <div class="kz-tile-value">{{ $tk }} {{ toBanglaNumber($salesMonth, 2) }}</div>
                <div class="kz-tile-sub">
                    {!! $delta($salesChangeMonth, __('kazitds::kazitds.vs same days last month')) !!}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kz-tile">
                <div class="kz-tile-icon kz-icon-green"><i class="fas fa-coins"></i></div>
                <div class="kz-tile-label">
                    @lang('kazitds::kazitds.Estimated Gross Profit')
                    <i class="fas fa-info-circle kz-muted" title="@lang('kazitds::kazitds.Net sales minus cost of goods sold at average purchase price, after approved sale returns.')"></i>
                </div>
                <div class="kz-tile-value">{{ $tk }} {{ toBanglaNumber($grossProfitMonth, 2) }}</div>
                <div class="kz-tile-sub kz-muted">
                    @lang('kazitds::kazitds.Margin'):
                    {{ $grossMargin === null ? '—' : toBanglaNumber($grossMargin, 1) . '%' }}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kz-tile">
                <div class="kz-tile-icon kz-icon-violet"><i class="fas fa-receipt"></i></div>
                <div class="kz-tile-label">@lang('kazitds::kazitds.Invoices This Month')</div>
                <div class="kz-tile-value">{{ toBanglaNumber($invoicesMonth) }}</div>
                <div class="kz-tile-sub kz-muted">
                    @lang('kazitds::kazitds.Average sale'): {{ $tk }} {{ toBanglaNumber($averageSaleMonth, 0) }}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('stock.reports') }}" class="kz-tile kz-tile-link">
                <div class="kz-tile-icon kz-icon-aqua"><i class="fas fa-warehouse"></i></div>
                <div class="kz-tile-label">@lang('kazitds::kazitds.Stock Value')</div>
                <div class="kz-tile-value">{{ $tk }} {{ toBanglaNumber($stockValue, 2) }}</div>
                <div class="kz-tile-sub kz-muted">
                    @lang('kazitds::kazitds.Units in stock'): {{ toBanglaNumber($stockUnits) }}
                </div>
            </a>
        </div>
    </div>

    {{-- ================= Trend + top products ================= --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            <div class="kz-card h-100">
                <div class="kz-card-header">
                    <h6>@lang('kazitds::kazitds.Sales vs Purchases')</h6>
                    <div class="btn-group btn-group-sm" role="group" aria-label="@lang('kazitds::kazitds.Date range')">
                        <button type="button" class="btn btn-outline-secondary kz-range" data-days="7">@lang('kazitds::kazitds.7 days')</button>
                        <button type="button" class="btn btn-outline-secondary kz-range active" data-days="30">@lang('kazitds::kazitds.30 days')</button>
                        <button type="button" class="btn btn-outline-secondary kz-range" data-days="90">@lang('kazitds::kazitds.90 days')</button>
                    </div>
                </div>
                <div class="kz-card-body">
                    <div id="kzTrendChart" class="kz-chart" role="img" aria-label="@lang('kazitds::kazitds.Daily sales and purchases')"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="kz-card h-100">
                <div class="kz-card-header">
                    <h6>@lang('kazitds::kazitds.Top Products This Month')</h6>
                    <a href="{{ route('product_variant_sales.reports') }}" class="kz-link">@lang('kazitds::kazitds.View All')</a>
                </div>
                <div class="kz-card-body">
                    @php $maxProductAmount = max($topProducts->max('total_amount') ?? 0, 1); @endphp
                    @forelse($topProducts as $product)
                        <div class="kz-bar-row">
                            <div class="kz-bar-head">
                                <span class="kz-bar-name" title="{{ $product->name }}">{{ $product->name }}</span>
                                <span class="kz-bar-value">{{ $tk }} {{ toBanglaNumber($product->total_amount, 0) }}</span>
                            </div>
                            <div class="kz-bar-track">
                                <div class="kz-bar-fill" style="width: {{ max(2, $product->total_amount / $maxProductAmount * 100) }}%"></div>
                            </div>
                            <div class="kz-muted kz-small">@lang('kazitds::kazitds.Units sold'): {{ toBanglaNumber($product->total_qty) }}</div>
                        </div>
                    @empty
                        <div class="kz-empty"><i class="fas fa-box-open"></i> @lang('kazitds::kazitds.No sales yet this month')</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Monthly sales + attention ================= --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            <div class="kz-card h-100">
                <div class="kz-card-header">
                    <h6>@lang('kazitds::kazitds.Monthly Sales (last 12 months)')</h6>
                    <a href="{{ route('sales.reports') }}" class="kz-link">@lang('kazitds::kazitds.Sales Report')</a>
                </div>
                <div class="kz-card-body">
                    <div id="kzMonthlyChart" class="kz-chart" role="img" aria-label="@lang('kazitds::kazitds.Monthly Sales (last 12 months)')"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="kz-card h-100">
                <div class="kz-card-header">
                    <h6>@lang('kazitds::kazitds.Needs Attention')</h6>
                </div>
                <div class="kz-card-body p-0">
                    @php
                        $alerts = [
                            ['count' => $outOfStockCount, 'label' => __('kazitds::kazitds.Out of stock'), 'icon' => 'times-circle', 'level' => 'critical', 'route' => route('low_stock.reports')],
                            ['count' => $lowStockCount, 'label' => __('kazitds::kazitds.Low stock (at or below :n)', ['n' => toBanglaNumber($lowStockThreshold)]), 'icon' => 'exclamation-triangle', 'level' => 'warning', 'route' => route('low_stock.reports')],
                            ['count' => $dueCustomerCount, 'label' => __('kazitds::kazitds.Customers with due'), 'icon' => 'user-clock', 'level' => 'warning', 'route' => route('due.index')],
                            ['count' => $pendingSaleReturns, 'label' => __('kazitds::kazitds.Pending sale returns'), 'icon' => 'undo', 'level' => 'info', 'route' => route('sale-returns.index')],
                            ['count' => $pendingPurchaseReturns, 'label' => __('kazitds::kazitds.Pending purchase returns'), 'icon' => 'truck-loading', 'level' => 'info', 'route' => route('purchase-returns.index')],
                        ];
                    @endphp
                    <ul class="kz-alerts">
                        @foreach($alerts as $alert)
                            <li>
                                <a href="{{ $alert['route'] }}" class="{{ $alert['count'] > 0 ? 'kz-alert-' . $alert['level'] : 'kz-alert-ok' }}">
                                    <i class="fas fa-{{ $alert['count'] > 0 ? $alert['icon'] : 'check-circle' }}"></i>
                                    <span class="flex-grow-1">{{ $alert['label'] }}</span>
                                    <span class="kz-alert-count">{{ toBanglaNumber($alert['count']) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Due customers + low stock ================= --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="kz-card h-100">
                <div class="kz-card-header">
                    <h6>@lang('kazitds::kazitds.Highest Customer Dues')</h6>
                    <a href="{{ route('due.index') }}" class="kz-link">@lang('kazitds::kazitds.View All')</a>
                </div>
                <div class="kz-card-body p-0">
                    <table class="table table-sm table-hover kz-table mb-0">
                        <thead>
                            <tr>
                                <th>@lang('kazitds::kazitds.Customer')</th>
                                <th>@lang('kazitds::kazitds.Phone')</th>
                                <th class="text-end">@lang('kazitds::kazitds.Due')</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topDueCustomers as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->phone ? toBanglaPhone($customer->phone) : '--' }}</td>
                                    <td class="text-end fw-bold">{{ $tk }} {{ toBanglaNumber($customer->due_amount, 2) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('due.payment', $customer->id) }}" class="btn btn-sm btn-encodex-payment py-0">@lang('kazitds::kazitds.Collect')</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="kz-empty"><i class="fas fa-check-circle"></i> @lang('kazitds::kazitds.No customer has any due')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="kz-card h-100">
                <div class="kz-card-header">
                    <h6>@lang('kazitds::kazitds.Low Stock Items')</h6>
                    <a href="{{ route('low_stock.reports') }}" class="kz-link">@lang('kazitds::kazitds.View All')</a>
                </div>
                <div class="kz-card-body p-0">
                    <table class="table table-sm table-hover kz-table mb-0">
                        <thead>
                            <tr>
                                <th>@lang('kazitds::kazitds.Product')</th>
                                <th>@lang('kazitds::kazitds.Brand')</th>
                                <th>@lang('kazitds::kazitds.Pack')</th>
                                <th class="text-end">@lang('kazitds::kazitds.Current Stock')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockItems as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->brand_name ?? '--' }}</td>
                                    <td>{{ $item->pack_name ?? '--' }}</td>
                                    <td class="text-end">
                                        @if($item->current_stock <= 0)
                                            <span class="kz-stock kz-stock-out"><i class="fas fa-times-circle"></i> {{ toBanglaNumber($item->current_stock) }}</span>
                                        @else
                                            <span class="kz-stock kz-stock-low"><i class="fas fa-exclamation-triangle"></i> {{ toBanglaNumber($item->current_stock) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="kz-empty"><i class="fas fa-check-circle"></i> @lang('kazitds::kazitds.All items are well stocked')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Recent activity ================= --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="kz-card h-100">
                <div class="kz-card-header">
                    <h6>@lang('kazitds::kazitds.Recent Sales')</h6>
                    <a href="{{ route('sales.index') }}" class="kz-link">@lang('kazitds::kazitds.View All')</a>
                </div>
                <div class="kz-card-body p-0">
                    <table class="table table-sm table-hover kz-table mb-0">
                        <thead>
                            <tr>
                                <th>@lang('kazitds::kazitds.Invoice')</th>
                                <th>@lang('kazitds::kazitds.Date')</th>
                                <th>@lang('kazitds::kazitds.Customer')</th>
                                <th class="text-end">@lang('kazitds::kazitds.Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td><a href="{{ route('sales.show', $sale->id) }}">{{ $sale->invoice_number }}</a></td>
                                    <td>{{ formatDate($sale->sale_date) }}</td>
                                    <td>{{ $sale->customer->name ?? $sale->customer_name ?? '--' }}</td>
                                    <td class="text-end fw-bold">{{ $tk }} {{ toBanglaNumber($sale->net_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="kz-empty">@lang('kazitds::kazitds.No data available')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="kz-card h-100">
                <div class="kz-card-header">
                    <h6>@lang('kazitds::kazitds.Recent Purchases')</h6>
                    <a href="{{ route('purchases.index') }}" class="kz-link">@lang('kazitds::kazitds.View All')</a>
                </div>
                <div class="kz-card-body p-0">
                    <table class="table table-sm table-hover kz-table mb-0">
                        <thead>
                            <tr>
                                <th>@lang('kazitds::kazitds.Purchase') #</th>
                                <th>@lang('kazitds::kazitds.Date')</th>
                                <th>@lang('kazitds::kazitds.Product')</th>
                                <th>@lang('kazitds::kazitds.Supplier')</th>
                                <th class="text-end">@lang('kazitds::kazitds.Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPurchases as $purchase)
                                <tr>
                                    <td><a href="{{ route('purchases.show', $purchase->id) }}">{{ $purchase->purchase_number }}</a></td>
                                    <td>{{ formatDate($purchase->purchase_date) }}</td>
                                    <td>{{ $purchase->productVariant->product->name ?? __('kazitds::kazitds.N/A') }}</td>
                                    <td>{{ $purchase->supplier->name ?? '--' }}</td>
                                    <td class="text-end fw-bold">{{ $tk }} {{ toBanglaNumber($purchase->total_price, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="kz-empty">@lang('kazitds::kazitds.No data available')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Quick access ================= --}}
    <div class="kz-card mb-4">
        <div class="kz-card-header">
            <h6>@lang('kazitds::kazitds.Quick Access')</h6>
        </div>
        <div class="kz-card-body">
            @php
                $shortcuts = [
                    ['route' => 'sales.create', 'icon' => 'plus-circle', 'title' => 'New Sale', 'sub' => 'Create an invoice'],
                    ['route' => 'purchases.create', 'icon' => 'cart-plus', 'title' => 'New Purchase', 'sub' => 'Add stock'],
                    ['route' => 'due.index', 'icon' => 'file-invoice-dollar', 'title' => 'Dues', 'sub' => 'Collect payments'],
                    ['route' => 'customers.index', 'icon' => 'users', 'title' => 'Customers', 'sub' => 'Manage Customers'],
                    ['route' => 'products.index', 'icon' => 'box', 'title' => 'Products', 'sub' => 'Manage Products'],
                    ['route' => 'product-variants.index', 'icon' => 'cubes', 'title' => 'Product Variants', 'sub' => 'Manage Product Variants'],
                    ['route' => 'stock.reports', 'icon' => 'warehouse', 'title' => 'Stock', 'sub' => 'View Inventory'],
                    ['route' => 'sales.reports', 'icon' => 'chart-bar', 'title' => 'Reports', 'sub' => 'Sales and purchase reports'],
                ];
            @endphp
            <div class="row g-2">
                @foreach($shortcuts as $shortcut)
                    <div class="col-xl-3 col-md-4 col-6">
                        <a href="{{ route($shortcut['route']) }}" class="kz-shortcut">
                            <i class="fas fa-{{ $shortcut['icon'] }}"></i>
                            <span>
                                <span class="d-block fw-bold">{{ __('kazitds::kazitds.' . $shortcut['title']) }}</span>
                                <span class="d-block kz-muted kz-small">{{ __('kazitds::kazitds.' . $shortcut['sub']) }}</span>
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    if (typeof ApexCharts === 'undefined') return;

    const css = getComputedStyle(document.querySelector('.kz-dash'));
    const color = name => css.getPropertyValue(name).trim();
    const tk = @json($tk);
    const nf = new Intl.NumberFormat(@json(app()->getLocale() === 'bn' ? 'bn-BD' : 'en-IN'), { maximumFractionDigits: 0 });
    const money = v => tk + ' ' + nf.format(v);

    const base = {
        chart: { toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'inherit', foreColor: color('--kz-text-secondary') },
        grid: { borderColor: color('--kz-grid'), strokeDashArray: 3, xaxis: { lines: { show: false } } },
        dataLabels: { enabled: false },
        yaxis: { labels: { formatter: v => nf.format(v) } },
        tooltip: { y: { formatter: money } },
    };

    // ---- Sales vs purchases (daily) ----
    const trend = new ApexCharts(document.querySelector('#kzTrendChart'), {
        ...base,
        chart: { ...base.chart, type: 'area', height: 320 },
        series: [
            { name: @json(__('kazitds::kazitds.Sales')), data: [] },
            { name: @json(__('kazitds::kazitds.Purchases')), data: [] },
        ],
        colors: [color('--kz-series-1'), color('--kz-series-2')],
        stroke: { width: 2, curve: 'straight' },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.25, opacityTo: 0.02 } },
        markers: { size: 0, hover: { size: 5 } },
        legend: { position: 'top', horizontalAlign: 'left' },
        tooltip: { ...base.tooltip, shared: true, intersect: false },
        xaxis: { categories: [], tickAmount: 10, labels: { rotate: 0, hideOverlappingLabels: true }, axisBorder: { show: false } },
        noData: { text: @json(__('kazitds::kazitds.Loading...')) },
    });
    trend.render();

    function loadTrend(days) {
        fetch(@json(url('/api/sales-chart-data')) + '?days=' + days, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                trend.updateOptions({ xaxis: { categories: data.labels } });
                trend.updateSeries([
                    { name: @json(__('kazitds::kazitds.Sales')), data: data.sales },
                    { name: @json(__('kazitds::kazitds.Purchases')), data: data.purchases },
                ]);
            })
            .catch(err => console.error('Error loading sales chart data:', err));
    }

    document.querySelectorAll('.kz-range').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.kz-range').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            loadTrend(btn.dataset.days);
        });
    });
    loadTrend(30);

    // ---- Monthly sales ----
    new ApexCharts(document.querySelector('#kzMonthlyChart'), {
        ...base,
        chart: { ...base.chart, type: 'bar', height: 300 },
        series: [{ name: @json(__('kazitds::kazitds.Sales')), data: @json($monthlySales['data']) }],
        colors: [color('--kz-series-1')],
        plotOptions: { bar: { borderRadius: 4, borderRadiusApplication: 'end', columnWidth: '55%' } },
        states: { hover: { filter: { type: 'darken', value: 0.85 } } },
        xaxis: { categories: @json($monthlySales['labels']), axisBorder: { show: false }, axisTicks: { show: false } },
        legend: { show: false },
    }).render();
})();
</script>
@endpush

@push('css')
<style>
    .kz-dash {
        --kz-surface: #ffffff;
        --kz-surface-2: #f7f7f5;
        --kz-border: #e6e5e1;
        --kz-grid: #ecebe7;
        --kz-text-primary: #1f1f1d;
        --kz-text-secondary: #5c5b57;
        --kz-text-muted: #85847e;
        --kz-series-1: #2a78d6;   /* sales */
        --kz-series-2: #eb6834;   /* purchases */
        --kz-good: #1a7f37;
        --kz-warning: #9a6700;
        --kz-critical: #cf222e;
        --kz-info: #2a78d6;
        color: var(--kz-text-primary);
    }

    .kz-muted { color: var(--kz-text-muted); }
    .kz-small { font-size: .78rem; }

    .kz-section-title {
        display: flex; align-items: baseline; gap: .75rem;
        font-weight: 700; font-size: .95rem; letter-spacing: .02em;
        margin: 0 0 .6rem;
    }
    .kz-section-title .kz-muted { font-weight: 400; font-size: .85rem; }

    /* ---- KPI tiles ---- */
    .kz-tile {
        display: block; position: relative; height: 100%;
        background: var(--kz-surface); border: 1px solid var(--kz-border); border-radius: 10px;
        padding: 1rem 1.1rem; color: inherit; text-decoration: none;
        transition: box-shadow .15s ease, transform .15s ease;
    }
    .kz-tile:hover { box-shadow: 0 6px 18px rgba(0, 0, 0, .06); }
    .kz-tile-link:hover { transform: translateY(-2px); color: inherit; }
    .kz-tile-icon {
        position: absolute; top: 1rem; right: 1rem;
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 1rem;
    }
    .kz-icon-blue   { background: #e6f0fb; color: #1c5cab; }
    .kz-icon-green  { background: #e3f4ea; color: #1a7f37; }
    .kz-icon-orange { background: #fdece4; color: #b8471b; }
    .kz-icon-red    { background: #fbe7e7; color: #b42318; }
    .kz-icon-violet { background: #ecebf8; color: #4a3aa7; }
    .kz-icon-aqua   { background: #e0f4ee; color: #117a55; }
    .kz-tile-label {
        font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em;
        color: var(--kz-text-secondary); padding-right: 48px; margin-bottom: .35rem;
    }
    .kz-tile-value { font-size: 1.45rem; font-weight: 700; line-height: 1.2; margin-bottom: .35rem; }
    .kz-tile-sub { font-size: .8rem; }
    .kz-delta { font-weight: 600; }
    .kz-up   { color: var(--kz-good); }
    .kz-down { color: var(--kz-critical); }

    /* ---- Cards ---- */
    .kz-card { background: var(--kz-surface); border: 1px solid var(--kz-border); border-radius: 10px; }
    .kz-card-header {
        display: flex; align-items: center; justify-content: space-between; gap: .5rem; flex-wrap: wrap;
        padding: .75rem 1rem; border-bottom: 1px solid var(--kz-border);
    }
    .kz-card-header h6 { margin: 0; font-weight: 700; }
    .kz-card-body { padding: 1rem; }
    .kz-link { font-size: .82rem; text-decoration: none; }
    .kz-chart { min-height: 300px; }
    .kz-range.active { background: var(--kz-text-secondary); border-color: var(--kz-text-secondary); color: #fff; }

    /* ---- Top products bar list ---- */
    .kz-bar-row + .kz-bar-row { margin-top: .85rem; }
    .kz-bar-head { display: flex; justify-content: space-between; gap: .5rem; font-size: .85rem; margin-bottom: .25rem; }
    .kz-bar-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .kz-bar-value { font-weight: 600; white-space: nowrap; }
    .kz-bar-track { height: 8px; border-radius: 4px; background: var(--kz-surface-2); overflow: hidden; }
    .kz-bar-fill { height: 100%; border-radius: 4px; background: var(--kz-series-1); }

    /* ---- Attention list ---- */
    .kz-alerts { list-style: none; margin: 0; padding: 0; }
    .kz-alerts li + li { border-top: 1px solid var(--kz-border); }
    .kz-alerts a {
        display: flex; align-items: center; gap: .75rem;
        padding: .8rem 1rem; text-decoration: none; color: var(--kz-text-primary);
    }
    .kz-alerts a:hover { background: var(--kz-surface-2); }
    .kz-alerts i { width: 18px; text-align: center; }
    .kz-alert-count { font-weight: 700; min-width: 2rem; text-align: right; }
    .kz-alert-critical i, .kz-alert-critical .kz-alert-count { color: var(--kz-critical); }
    .kz-alert-warning i,  .kz-alert-warning .kz-alert-count  { color: var(--kz-warning); }
    .kz-alert-info i,     .kz-alert-info .kz-alert-count     { color: var(--kz-info); }
    .kz-alert-ok i { color: var(--kz-good); }
    .kz-alert-ok .kz-alert-count { color: var(--kz-text-muted); }

    /* ---- Tables ---- */
    .kz-table th { font-size: .75rem; text-transform: uppercase; letter-spacing: .03em; color: var(--kz-text-secondary); font-weight: 600; }
    .kz-table th:first-child, .kz-table td:first-child { padding-left: 1rem; }
    .kz-table th:last-child, .kz-table td:last-child { padding-right: 1rem; }
    .kz-table td { vertical-align: middle; font-size: .88rem; }
    .kz-stock { font-weight: 700; white-space: nowrap; }
    .kz-stock-out { color: var(--kz-critical); }
    .kz-stock-low { color: var(--kz-warning); }
    .kz-empty { text-align: center; color: var(--kz-text-muted); padding: 1.5rem 1rem !important; }

    /* ---- Quick access ---- */
    .kz-shortcut {
        display: flex; align-items: center; gap: .75rem; height: 100%;
        padding: .7rem .85rem; border: 1px solid var(--kz-border); border-radius: 8px;
        text-decoration: none; color: var(--kz-text-primary); background: var(--kz-surface);
        transition: background .15s ease, border-color .15s ease;
    }
    .kz-shortcut:hover { background: var(--kz-surface-2); border-color: var(--kz-series-1); color: var(--kz-text-primary); }
    .kz-shortcut > i { font-size: 1.2rem; color: var(--kz-series-1); width: 24px; text-align: center; }

    @media (max-width: 576px) {
        .kz-tile-value { font-size: 1.25rem; }
        .kz-card-header { padding: .6rem .75rem; }
        .kz-card-body { padding: .75rem; }
    }
</style>
@endpush
