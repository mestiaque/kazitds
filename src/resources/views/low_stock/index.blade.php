@extends('me::master')

@section('title', __('kazitds::kazitds.Low Stock Products'))

@push('buttons')
    @component('me::components.btn.add-button', [
        'route' => route('products.index'),
        'text' => __('kazitds::kazitds.All Products'),
        'class' => 'btn-encodex-list'
    ])
    @endcomponent
@endpush

@section('content')
<div class="container-fluidx">
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover table-encodex">
                    <thead class="">
                        <tr>
                            <th>@lang('kazitds::kazitds.#')</th>
                            <th>@lang('kazitds::kazitds.Product')</th>
                            <th>@lang('kazitds::kazitds.Variant')</th>
                            <th>@lang('kazitds::kazitds.Current Stock')</th>
                            <th>@lang('kazitds::kazitds.Threshold')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $threshold = \ME\Kazitds\Models\LowStock::getThreshold();
                        @endphp
                        @forelse($lowStockItems as $variant)
                            <tr>
                                <td>{{ toBanglaNumber($loop->iteration) }}</td>
                                <td>{{ $variant->product->name ?? '-' }}</td>
                                <td>{{ $variant->brand->name ?? '-' }} - {{ $variant->pack->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-danger text-white">
                                        {{ toBanglaNumber($variant->getCurrentStock()) }}
                                    </span>
                                </td>
                                <td>{{ toBanglaNumber($threshold) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    @lang('kazitds::kazitds.No low stock products found.')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
