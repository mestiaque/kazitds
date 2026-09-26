@extends('me::master')

@section('title', trans('kazitds::kazitds.Product Variant Wise Sales Report'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('product_variant_sales.reports.print') . '?' . http_build_query(request()->query()),
      'text' => __('kazitds::kazitds.Print'),
      'bg' => 'info',
      'icon' => 'print',
      'attribute' => 'target="_blank"',
      'class' => 'btn-encodex-print2'
  ])
  @endcomponent
@endpush

@section('content')

<div class="card shadow mb-4 w-100">
    <div class="card-body">
        <form method="GET" action="{{ route('product_variant_sales.reports') }}">
            <div class="row">
                <div class="col-md-2 mb-3">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                        </div>
                    </div>
                </div>

                <div class="col-md-2 mb-3">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="form-group">
                        <select name="product_variant_id" class="form-select form-select-sm" data-control="select2">
                            <option value="">@lang('kazitds::kazitds.All Variants')</option>
                            @foreach($productVariantsFilter as $pv)
                                <option value="{{ $pv->id }}" {{ request('product_variant_id') == $pv->id ? 'selected' : '' }}>
                                    {{ $pv->getVariantName() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2 mb-3">
                    <div class="d-flex">
                        <button type="submit" class="btn btn-sm btn-encodex-search me-1">
                            <i class="fas fa-search me-1"></i> @lang('kazitds::kazitds.Search')
                        </button>
                            <a href="{{ route('product_variant_sales.reports') }}" class="btn btn-encodex-clear btn-sm">
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
                        <th>@lang('kazitds::kazitds.Variant')</th>
                        <th class="text-end">@lang('kazitds::kazitds.Avg. Price')</th>
                        <th class="text-end">@lang('kazitds::kazitds.Total Quantity')</th>
                        <th class="text-end">@lang('kazitds::kazitds.Total Amount')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productVariantSales as $item)
                        @php
                            $variant = $productVariants->get($item->product_variant_id);
                        @endphp
                        <tr>
                            <td>{{ toBanglaNumber($loop->iteration) }}</td>
                            <td>
                                @if($variant)
                                    <span class="">
                                        {{ $variant->getVariantName() }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end">{{ toBanglaNumber($item->avg_price,2) }}</td>
                            <td class="text-end">{{ toBanglaNumber($item->total_quantity) }}</td>
                            <td class="text-end">{{ toBanglaNumber($item->total_amount,2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">@lang('kazitds::kazitds.No data found')</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">@lang('kazitds::kazitds.Total')</td>
                        <td class="te5t-end">{{ toBanglaNumber($totalQuantity) }}</td>
                        <td class="text-end">{{ toBanglaNumber($totalAmount,2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{ $productVariantSales->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
