@extends('me::master')

@section('title', trans('kazitds::kazitds.Purchase Report'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('purchases.reports.print') . '?' . http_build_query(request()->query()),
      'text' => __('kazitds::kazitds.Print'),
      'icon' => 'print',
      'attribute' => 'target="_blank"',
      'class' => 'btn-encodex-print2'
  ])
  @endcomponent
@endpush

@section('content')
@php $tk = __('kazitds::kazitds.TK.'); @endphp

<div class="card shadow mb-4 w-100">
    <div class="card-body">
        <form method="GET" action="{{ route('purchases.reports') }}">
            <input type="hidden" name="view" value="{{ $view }}">
            <div class="row g-2 mb-3">
                <div class="col-md-2">
                    <input type="date" name="start_date" title="@lang('kazitds::kazitds.Start Date')" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="end_date" title="@lang('kazitds::kazitds.End Date')" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <select name="supplier_id" class="form-select form-select-sm" data-control="select2">
                        <option value="">@lang('kazitds::kazitds.All Suppliers')</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="product_variant_id" class="form-select form-select-sm" data-control="select2">
                        <option value="">@lang('kazitds::kazitds.All Products')</option>
                        @foreach($productVariants as $variant)
                            <option value="{{ $variant->id }}" {{ request('product_variant_id') == $variant->id ? 'selected' : '' }}>
                                {{ $variant->product->name }} - {{ $variant->brand->name ?? '--' }} - {{ $variant->pack->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex">
                    <button type="submit" class="btn btn-sm btn-encodex-search me-1">
                        <i class="fas fa-search me-1"></i> @lang('kazitds::kazitds.Search')
                    </button>
                    <a href="{{ route('purchases.reports', ['view' => $view]) }}" class="btn btn-sm btn-encodex-clear">
                        <i class="fas fa-eraser me-1"></i> @lang('kazitds::kazitds.Reset')
                    </a>
                </div>
            </div>
        </form>

        {{-- View toggle + summary --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'product', 'page' => null]) }}" class="btn {{ $view === 'product' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-boxes me-1"></i> @lang('kazitds::kazitds.Product-wise')
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'list', 'page' => null]) }}" class="btn {{ $view === 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-list me-1"></i> @lang('kazitds::kazitds.Purchase-wise')
                </a>
            </div>
            <div class="small text-muted">
                @lang('kazitds::kazitds.Purchases'): <strong>{{ toBanglaNumber($totals->purchases) }}</strong>
                · @lang('kazitds::kazitds.Quantity'): <strong>{{ toBanglaNumber($totals->quantity) }}</strong>
                · @lang('kazitds::kazitds.Total'): <strong>{{ $tk }} {{ toBanglaNumber($totals->amount, 2) }}</strong>
            </div>
        </div>

        <div class="table-responsive">
            @if($view === 'product')
                <table class="table table-bordered table-sm table-hover table-encodex">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('kazitds::kazitds.Product')</th>
                            <th>@lang('kazitds::kazitds.Brand')</th>
                            <th>@lang('kazitds::kazitds.Pack')</th>
                            <th class="text-center">@lang('kazitds::kazitds.Purchases')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Quantity')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Avg. Purchase Price')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Total')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td>{{ toBanglaNumber($rows->firstItem() + $loop->index) }}</td>
                                <td><strong>{{ $row->variant->product->name ?? __('kazitds::kazitds.N/A') }}</strong></td>
                                <td>{{ $row->variant->brand->name ?? '--' }}</td>
                                <td>{{ $row->variant->pack->name ?? '--' }}</td>
                                <td class="text-center"><span class="badge bg-secondary">{{ toBanglaNumber($row->purchase_count) }}</span></td>
                                <td class="text-end">{{ toBanglaNumber($row->total_quantity) }}</td>
                                <td class="text-end">{{ $tk }} {{ toBanglaNumber($row->avg_price, 2) }}</td>
                                <td class="text-end fw-bold">{{ $tk }} {{ toBanglaNumber($row->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-3 text-muted"><i class="fas fa-info-circle me-1"></i> @lang('kazitds::kazitds.No data available')</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">@lang('kazitds::kazitds.Grand Total')</td>
                            <td class="text-end">{{ toBanglaNumber($totals->quantity) }}</td>
                            <td></td>
                            <td class="text-end">{{ $tk }} {{ toBanglaNumber($totals->amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <table class="table table-bordered table-sm table-hover table-encodex">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('kazitds::kazitds.Purchase Number')</th>
                            <th>@lang('kazitds::kazitds.Date')</th>
                            <th>@lang('kazitds::kazitds.Supplier')</th>
                            <th>@lang('kazitds::kazitds.Product')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Quantity')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Unit Price')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Total')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $purchase)
                            <tr>
                                <td>{{ toBanglaNumber($rows->firstItem() + $loop->index) }}</td>
                                <td><span class="badge bg-success">{{ $purchase->purchase_number }}</span></td>
                                <td>{{ formatDate($purchase->purchase_date) }}</td>
                                <td>
                                    @if($purchase->supplier)
                                        <strong>{{ $purchase->supplier->name }}</strong>
                                        @if($purchase->supplier->company_name)
                                            <br><small class="text-muted">{{ $purchase->supplier->company_name }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">@lang('kazitds::kazitds.N/A')</span>
                                    @endif
                                </td>
                                <td><strong>{{ $purchase->productVariant->brand->name ?? '' }} {{ $purchase->productVariant->product->name }} {{ $purchase->productVariant->pack->name }}</strong></td>
                                <td class="text-end">{{ toBanglaNumber($purchase->quantity) }}</td>
                                <td class="text-end">{{ $tk }} {{ toBanglaNumber($purchase->price_per_unit, 2) }}</td>
                                <td class="text-end fw-bold">{{ $tk }} {{ toBanglaNumber($purchase->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-3 text-muted"><i class="fas fa-info-circle me-1"></i> @lang('kazitds::kazitds.No data available')</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">@lang('kazitds::kazitds.Grand Total')</td>
                            <td class="text-end">{{ toBanglaNumber($totals->quantity) }}</td>
                            <td></td>
                            <td class="text-end">{{ $tk }} {{ toBanglaNumber($totals->amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            @endif

            {{ $rows->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
