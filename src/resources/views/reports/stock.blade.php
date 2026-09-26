@extends('me::master')

@section('title', trans('kazitds::kazitds.Stock Report'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('stock.reports.print'),
      'text' => __('kazitds::kazitds.Print'),
      'bg' => 'info',
      'icon' => 'print',
      'attributes' => 'target="_blank"',
      'class' => 'btn-encodex-print2'
  ])
  @endcomponent
@endpush

@section('content')

<div class="card shadow mb-4 w-100">
    <div class="card-header py-3 bg-info d-none">
        <div class="container-fluid px-0">
            <div class="row align-items-center">
                <!-- Title Column -->
                <div class="col-md-6 col-12 mb-2 mb-md-0">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-warehouse mr-2"></i> @lang('kazitds::kazitds.Stock Report')
                    </h6>
                </div>

                <!-- Summary Badges Column -->
                <div class="col-md-6 col-12 text-md-end">
                    <div class="summary-badges">
                        <span class="badge badge-sm bg-light badge-pill p-1 shadow-sm me-2 text-dark">
                            <i class="fas fa-money-bill-wave me-1"></i> @lang('kazitds::kazitds.Total Value'):
                            <span class="font-weight-bold text-info">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($totalStockValue, 2) }}</span>
                        </span>
                        <span class="badge badge-sm bg-light badge-pill p-1 shadow-sm text-dark">
                            <i class="fas fa-boxes me-1"></i> @lang('kazitds::kazitds.Total Items'):
                            <span class="font-weight-bold text-primary">{{ toBanglaNumber($totalItems) }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('stock.reports') }}">
            <div class="row">
                <div class="col-md-2 mb-3">
                    <div class="form-group">
                        <select name="product" class="form-select form-select-sm" data-control="select2">
                            <option value="">@lang('kazitds::kazitds.All Products')</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2 mb-3">
                    <div class="form-group">
                        <select name="brand" class="form-select form-select-sm" data-control="select2">
                            <option value="">@lang('kazitds::kazitds.All Brands')</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2 mb-3">
                    <div class="form-group">
                        <select name="show_zero_stock" class="form-select form-select-sm">
                            <option value="1" {{ request('show_zero_stock', '1') == '1' ? 'selected' : '' }}>@lang('kazitds::kazitds.Show All')</option>
                            <option value="0" {{ request('show_zero_stock') === '0' ? 'selected' : '' }}>@lang('kazitds::kazitds.Hide Zero Stock')</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2 mb-3">
                    <div class="d-flex">
                        <button type="submit" class="btn btn-sm btn-encodex-search me-1">
                            <i class="fas fa-search me-1"></i> @lang('kazitds::kazitds.Search')
                        </button>
                        <a href="{{ route('stock.reports') }}" class="btn btn-sm btn-encodex-clear">
                            <i class="fas fa-eraser me-1"></i> @lang('kazitds::kazitds.Reset')
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover table-encodex">
                <thead class="">
                    <tr>
                        <th>#</th>
                        <th>@lang('kazitds::kazitds.Product')</th>
                        <th>@lang('kazitds::kazitds.Variant')</th>
                        <th class="text-end">@lang('kazitds::kazitds.Avg. Purchase Price')</th>
                        <th class="text-end">@lang('kazitds::kazitds.Current Stock')</th>
                        <th class="text-end">@lang('kazitds::kazitds.Stock Value')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockReport as $variant)
                        <tr class="{{ $variant->current_stock <= 0 ? 'table-danger' : ($variant->current_stock < 5 ? 'table-warning' : '') }}">
                            <td>{{ toBanglaNumber($loop->iteration) }}</td>
                            <td><strong>{{ $variant->product->name }}</strong></td>
                            <td>{{ $variant->brand->name ?? '--' }} - {{ $variant->pack->name }}</td>
                            <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($variant->avg_purchase_price, 2) }}</td>
                            <td class="text-end font-weight-bold">

                                @if($variant->current_stock <= 0)
                                    <span class="badge bg-danger">{{ toBanglaNumber($variant->current_stock) }}</span>
                                @elseif($variant->current_stock < 5)
                                    <span class="badge bg-warning">{{ toBanglaNumber($variant->current_stock) }}</span>
                                @else
                                    <span class="badge bg-success">{{ toBanglaNumber($variant->current_stock) }}</span>
                                @endif
                            </td>
                            <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($variant->stock_value, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <div class="py-3 text-muted">
                                    <i class="fas fa-info-circle me-1"></i> @lang('kazitds::kazitds.No data available')
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light">
                    <tr class="fw-bold">
                        <td colspan="4" class="text-end">@lang('kazitds::kazitds.Total')</td>
                        <td class="text-end">{{ toBanglaNumber($totalItems) }}</td>
                        <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($totalStockValue, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>
@endsection

