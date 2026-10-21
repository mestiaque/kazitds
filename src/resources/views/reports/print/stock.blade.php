@extends('me::printMaster2')

@section('title', __('kazitds::kazitds.Stock Report'))

@section('contents')
    @include('kazitds::reports.print._header', [
        'reportTitle' => __('kazitds::kazitds.Stock Report'),
        'lines' => [],
    ])

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>@lang("kazitds::kazitds.Product")</th>
                <th>@lang("kazitds::kazitds.Brand")</th>
                <th>@lang("kazitds::kazitds.Pack")</th>
                <th class="text-end">@lang("kazitds::kazitds.Avg. Purchase Price")</th>
                <th class="text-end">@lang("kazitds::kazitds.Current Stock")</th>
                <th class="text-end">@lang("kazitds::kazitds.Stock Value")</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockReport as $variant)
                <tr>
                    <td>{{ toBanglaNumber($loop->iteration) }}</td>
                    <td>{{ $variant->product->name }}</td>
                    <td>{{ $variant->brand->name ?? '--' }}</td>
                    <td>{{ $variant->pack->name }}</td>
                    <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($variant->avg_purchase_price, 2) }}</td>
                    <td class="text-end">{{ toBanglaNumber($variant->current_stock) }}</td>
                    <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($variant->stock_value, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">@lang("kazitds::kazitds.No data available")</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-end">@lang("kazitds::kazitds.Grand Total")</td>
                <td class="text-end">{{ toBanglaNumber($totalItems) }}</td>
                <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($totalStockValue, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @include('kazitds::reports.print._footer')
@endsection
