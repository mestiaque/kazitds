@extends('me::printMaster2')

@section('title', __('kazitds::kazitds.Product Variant Wise Sales Report'))

@section('contents')
    @include('kazitds::reports.print._header', [
        'reportTitle' => __('kazitds::kazitds.Product Variant Wise Sales Report'),
        'lines' => [__('kazitds::kazitds.Period') . ': ' . $startDate . ' ' . __('kazitds::kazitds.to') . ' ' . $endDate],
    ])

    <table class="table table-bordered table-sm">
        <thead>
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
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($variant)
                            {{ $variant->getVariantName() }}
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
                <td class="text-end">{{ toBanglaNumber($totalQuantity) }}</td>
                <td class="text-end">{{ toBanglaNumber($totalAmount,2) }}</td>
            </tr>
        </tfoot>
    </table>

    @include('kazitds::reports.print._footer')
@endsection
