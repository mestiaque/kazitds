@extends('me::printMaster2')

@section('title', __('kazitds::kazitds.Purchase Report') . ' (' . ($view === 'product' ? __('kazitds::kazitds.Product-wise') : __('kazitds::kazitds.Purchase-wise')) . ')')

@section('contents')
    @include('kazitds::reports.print._header', [
        'reportTitle' => __('kazitds::kazitds.Purchase Report') . ' (' . ($view === 'product' ? __('kazitds::kazitds.Product-wise') : __('kazitds::kazitds.Purchase-wise')) . ')',
        'lines' => [__('kazitds::kazitds.Period') . ': ' . $startDate . ' ' . __('kazitds::kazitds.to') . ' ' . $endDate, $supplierName ? __('kazitds::kazitds.Supplier') . ': ' . $supplierName : null],
    ])

    <table>
        @if($view === 'product')
        <thead>
            <tr>
                <th>#</th>
                <th>@lang("kazitds::kazitds.Product")</th>
                <th class="text-center">@lang("kazitds::kazitds.Purchases")</th>
                <th class="text-end">@lang("kazitds::kazitds.Quantity")</th>
                <th class="text-end">@lang("kazitds::kazitds.Avg. Purchase Price")</th>
                <th class="text-end">@lang("kazitds::kazitds.Total")</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ toBanglaNumber($loop->iteration) }}</td>
                    <td>{{ $row->variant->product->name ?? __('kazitds::kazitds.N/A') }} - {{ $row->variant->brand->name ?? '--' }} - {{ $row->variant->pack->name ?? '--' }}</td>
                    <td class="text-center">{{ toBanglaNumber($row->purchase_count) }}</td>
                    <td class="text-end">{{ toBanglaNumber($row->total_quantity) }}</td>
                    <td class="text-end">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($row->avg_price, 2) }}</td>
                    <td class="text-end">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($row->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">@lang('kazitds::kazitds.No data available')</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-end">@lang("kazitds::kazitds.Grand Total")</td>
                <td class="text-end">{{ toBanglaNumber($totals->quantity) }}</td>
                <td></td>
                <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($totals->amount, 2) }}</td>
            </tr>
        </tfoot>
        @else
        <thead>
            <tr>
                <th>#</th>
                <th>@lang("kazitds::kazitds.Purchase Number")</th>
                <th>@lang("kazitds::kazitds.Date")</th>
                <th>@lang("kazitds::kazitds.Supplier")</th>
                <th>@lang("kazitds::kazitds.Product")</th>
                <th class="text-end">@lang("kazitds::kazitds.Quantity")</th>
                <th class="text-end">@lang("kazitds::kazitds.Unit Price")</th>
                <th class="text-end">@lang("kazitds::kazitds.Total")</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $purchase)
                <tr>
                    <td>{{ toBanglaNumber($loop->iteration) }}</td>
                    <td>{{ $purchase->purchase_number }}</td>
                    <td>{{ formatDate($purchase->purchase_date) }}</td>
                    <td>{{ $purchase->supplier->name ?? __('kazitds::kazitds.N/A') }}</td>
                    <td>{{ $purchase->productVariant->product->name }} - {{ $purchase->productVariant->brand->name ?? '--' }} - {{ $purchase->productVariant->pack->name }}</td>
                    <td class="text-end">{{ toBanglaNumber($purchase->quantity) }}</td>
                    <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($purchase->price_per_unit, 2) }}</td>
                    <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($purchase->total_price, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">@lang("kazitds::kazitds.No data available")</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-end">@lang("kazitds::kazitds.Grand Total")</td>
                <td class="text-end">{{ toBanglaNumber($totals->quantity) }}</td>
                <td></td>
                <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($totals->amount, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    @include('kazitds::reports.print._footer')
@endsection
