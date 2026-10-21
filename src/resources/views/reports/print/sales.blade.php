@extends('me::printMaster2')

@section('title', __('kazitds::kazitds.Sales Report') . ' (' . ($view === 'product' ? __('kazitds::kazitds.Product-wise') : __('kazitds::kazitds.Invoice-wise')) . ')')

@section('contents')
    @include('kazitds::reports.print._header', [
        'reportTitle' => __('kazitds::kazitds.Sales Report') . ' (' . ($view === 'product' ? __('kazitds::kazitds.Product-wise') : __('kazitds::kazitds.Invoice-wise')) . ')',
        'lines' => [__('kazitds::kazitds.Period') . ': ' . $startDate . ' ' . __('kazitds::kazitds.to') . ' ' . $endDate, __('kazitds::kazitds.Note: Sales with returns are excluded from this report.')],
    ])

    <table>
        @if($view === 'product')
        <thead>
            <tr>
                <th>#</th>
                <th>@lang('kazitds::kazitds.Product')</th>
                <th class="text-center">@lang('kazitds::kazitds.Invoices')</th>
                <th class="text-end">@lang('kazitds::kazitds.Quantity')</th>
                <th class="text-end">@lang('kazitds::kazitds.Avg. Price')</th>
                <th class="text-end">@lang('kazitds::kazitds.Total')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ toBanglaNumber($loop->iteration) }}</td>
                    <td>{{ $row->variant->product->name ?? __('kazitds::kazitds.N/A') }} - {{ $row->variant->brand->name ?? '--' }} - {{ $row->variant->pack->name ?? '--' }}</td>
                    <td class="text-center">{{ toBanglaNumber($row->invoice_count) }}</td>
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
                <td colspan="3" class="text-end">@lang('kazitds::kazitds.Grand Total')</td>
                <td class="text-end">{{ toBanglaNumber($itemTotals->quantity) }}</td>
                <td></td>
                <td class="text-end">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($itemTotals->amount, 2) }}</td>
            </tr>
            @if(!request('product_variant_id') && $totals->discount > 0)
                <tr class="total-row">
                    <td colspan="5" class="text-end">(-) @lang('kazitds::kazitds.Invoice Discount')</td>
                    <td class="text-end">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($totals->discount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="5" class="text-end">@lang('kazitds::kazitds.Net Sales')</td>
                    <td class="text-end">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($totals->net, 2) }}</td>
                </tr>
            @endif
        </tfoot>
        @else
        <thead>
            <tr>
                <th>@lang('kazitds::kazitds.#')</th>
                <th>@lang('kazitds::kazitds.Invoice Number')</th>
                <th>@lang('kazitds::kazitds.Date')</th>
                <th>@lang('kazitds::kazitds.Customer')</th>
                <th>@lang('kazitds::kazitds.Items')</th>
                @if(get_setting('show_discount_option'))
                    <th class="text-end">@lang('kazitds::kazitds.Subtotal')</th>
                    <th class="text-end">@lang('kazitds::kazitds.Discount')</th>
                @endif
                <th class="text-end">@lang('kazitds::kazitds.Total')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $sale)
                <tr>
                    <td>{{ toBanglaNumber($loop->iteration) }}</td>
                    <td>{{ $sale->invoice_number }}</td>
                    <td>{{ formatDate($sale->sale_date) }}</td>
                    <td>{{ $sale->customer_name ?? '--' }}</td>
                    <td class="text-end">{{ toBanglaNumber($sale->items->count()) }}</td>
                    @if(get_setting('show_discount_option'))
                        <td class="text-end">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($sale->total_amount, 2) }}</td>
                        <td class="text-end">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($sale->discount, 2) }}</td>
                    @endif
                    <td class="text-end">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($sale->net_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">@lang('kazitds::kazitds.No data available')</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-end">@lang('kazitds::kazitds.Grand Total')</td>
                @if(get_setting('show_discount_option'))
                    <td class="text-end">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($totals->subtotal, 2) }}</td>
                    <td class="text-end">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($totals->discount, 2) }}</td>
                @endif
                <td class="text-end">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($totals->net, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    @include('kazitds::reports.print._footer')
@endsection
