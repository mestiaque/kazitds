<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="{{ asset('assets/img/favicon/Encodex.ico') }}" type="image/x-icon">

    <title>@lang("kazitds::kazitds.Purchase Report")</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }

        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .report-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .report-header p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .no-print {
            /* display: none; */
        }
        .report-footer p{
            margin: 1px !important;
            margin-bottom: 2px !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
                font-size: 10px;
            }

            .report-header h1 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="no-print bg-darks" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer; background-color: #4e73df; color: white; border: none; border-radius: 4px;">
            <i class="fas fa-print" style="margin-right: 5px;"></i> @lang("kazitds::kazitds.Print Report")
        </button>
    </div>

    <div class="report-header">
        <h1>{{ get_setting('shop_name', config('app.name')) }}</h1>
        <p>{{ get_setting('shop_address', '') }}</p>
        <p>@lang("kazitds::kazitds.Phone"): {{ get_setting('shop_phone', '') }} | @lang("kazitds::kazitds.Email"): {{ get_setting('shop_email', '') }}</p>
        <p>@lang("kazitds::kazitds.Purchase Report") ({{ $view === 'product' ? __('kazitds::kazitds.Product-wise') : __('kazitds::kazitds.Purchase-wise') }})</p>
        <p>@lang("kazitds::kazitds.Period"): {{ $startDate }} @lang("kazitds::kazitds.to") {{ $endDate }}</p>
        @if($supplierName)
            <p>@lang("kazitds::kazitds.Supplier"): {{ $supplierName }}</p>
        @endif
    </div>

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

    <div class="report-footer text-center">
        <p class="mb-1">@lang('kazitds::kazitds.Report generated by') {{ get_setting('shop_name', config('app.name')) }} @lang('kazitds::kazitds.on') {{ formatDate(now()) }} | @lang('kazitds::kazitds.Developed by: ENcodeX') </p>
        <p class="text-center">*** @lang("kazitds::kazitds.End of Report") ***</p>
    </div>

    <script>
        window.onload = function() {
            // Auto print when page is loaded
            setTimeout(function() {
                window.print();
            }, 1000);
        }
    </script>
</body>
</html>
