@extends('me::master')

@section('title', 'Dashboard')

@section('content')

<!-- Dashboard Header Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            @lang('kazitds::kazitds.Today\'s Sales')</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">TK.  {{ toBanglaNumber($todaySales ?? 0, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            @lang('kazitds::kazitds.Total Sales (Monthly)')</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">TK.  {{ toBanglaNumber($monthlySales ?? 0, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            @lang('kazitds::kazitds.Total Stock Value')
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">TK.  {{ toBanglaNumber($stockValue ?? 0, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            @lang('kazitds::kazitds.Low Stock Items')</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockCount ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Sales Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">@lang('kazitds::kazitds.Sales Overview')</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">@lang('kazitds::kazitds.Time Range'):</div>
                        <a class="dropdown-item chart-filter" data-range="7" href="#">@lang('kazitds::kazitds.Last 7 Days')</a>
                        <a class="dropdown-item chart-filter" data-range="30" href="#">@lang('kazitds::kazitds.Last 30 Days')</a>
                        <a class="dropdown-item chart-filter" data-range="90" href="#">@lang('kazitds::kazitds.Last 3 Months')</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('reports.sales') }}">@lang('kazitds::kazitds.View Detailed Report')</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">@lang('kazitds::kazitds.Sales by Category')</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="categoryPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small category-labels">
                    <!-- This will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Recent Sales -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">@lang('kazitds::kazitds.Recent Sales')</h6>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-encodex">
                    @lang('kazitds::kazitds.View All')
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>@lang('kazitds::kazitds.Invoice')</th>
                                <th>@lang('kazitds::kazitds.Customer')</th>
                                <th>@lang('kazitds::kazitds.Date')</th>
                                <th>@lang('kazitds::kazitds.Amount')</th>
                                <th>@lang('kazitds::kazitds.Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales ?? [] as $sale)
                            <tr>
                                <td>{{ $sale->invoice_number }}</td>
                                <td>{{ $sale->customer_name ?? '--' }}</td>
                                <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                <td>TK.  {{ toBanglaNumber($sale->net_amount, 2) }}</td>
                                <td>
                                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">@lang('kazitds::kazitds.No recent sales found')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Items -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">@lang('kazitds::kazitds.Low Stock Items')</h6>
                <a href="{{ route('reports.stock') }}" class="btn btn-sm btn-encodex">
                    @lang('kazitds::kazitds.View All')
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>@lang('kazitds::kazitds.Product')</th>
                                <th>@lang('kazitds::kazitds.Brand')</th>
                                <th>@lang('kazitds::kazitds.Pack')</th>
                                <th>@lang('kazitds::kazitds.Stock')</th>
                                <th>@lang('kazitds::kazitds.Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockItems ?? [] as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->brand->name ?? '--' }}</td>
                                <td>{{ $item->pack->name }}</td>
                                <td>
                                    <span class="badge badge-danger">{{ $item->current_stock }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('purchases.create', ['product_variant_id' => $item->id]) }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-plus"></i> @lang('kazitds::kazitds.Purchase')
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">@lang('kazitds::kazitds.No low stock items')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($salesChartData['labels'] ?? ['No Data']),
            datasets: [{
                label: 'Sales Amount',
                data: @json($salesChartData['data'] ?? [0]),
                fill: false,
                borderColor: '#4e73df',
                tension: 0.1
            }]
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                y: {
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            return 'TK.  ' + value;
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Sales: TK.  ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });

    // Category Pie Chart
    const categoryLabels = @json($categoryChartData['labels'] ?? ['No Data']);
    const categoryData = @json($categoryChartData['data'] ?? [100]);
    const backgroundColors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69',
        '#6610f2', '#fd7e14', '#20c9a6', '#858796', '#5a5c69', '#e74a3b'
    ];

    const categoryCtx = document.getElementById('categoryPieChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryData,
                backgroundColor: backgroundColors.slice(0, categoryLabels.length),
                hoverBackgroundColor: backgroundColors.slice(0, categoryLabels.length),
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: ${percentage}% (TK.  ${value})`;
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });

    // Create category labels with colors
    const labelContainer = document.querySelector('.category-labels');
    categoryLabels.forEach((label, index) => {
        const span = document.createElement('span');
        span.classList.add('mr-2');
        span.innerHTML = `<i class="fas fa-circle" style="color:${backgroundColors[index]};"></i> ${label}`;
        labelContainer.appendChild(span);
    });

    // Chart filter event handlers
    document.querySelectorAll('.chart-filter').forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
            const days = event.target.dataset.range;

            // Make AJAX call to get new data
            fetch(`/api/sales-chart-data?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    // Update chart with new data
                    salesChart.data.labels = data.labels;
                    salesChart.data.datasets[0].data = data.data;
                    salesChart.update();
                })
                .catch(error => console.error('Error fetching chart data:', error));
        });
    });
});
</script>
@endpush
