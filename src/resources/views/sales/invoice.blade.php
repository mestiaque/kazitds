<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="{{ asset('assets/img/favicon/Encodex.ico') }}" type="image/x-icon">

    <title>@lang("kazitds::kazitds.Invoice") #{{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px; /* Reduced font size */
            color: #333;
        }
        /* Header Flex Layout */
        .invoice-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 5px; /* Reduced margin */
            border-bottom: 1px solid #333; /* Thinner border */
            padding-bottom: 2px; /* Reduced padding */
            position: relative;
            z-index: 2;
        }
        .header-logo {
            width: 90px; /* Smaller logo */
            height: 90px; /* Smaller logo */
            object-fit: contain;
        }
        .header-info {
            flex: 1;
            text-align: center;
        }
        .header-info p {
            margin: 1px 0; /* Reduced margin */
            font-size: 13px; /* Smaller font size */
        }
        .header-qrcode {
            width: 90px; /* Smaller QR code */
            text-align: right;
        }

        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px; /* Reduced padding */
            box-sizing: border-box;
            position: relative;
            z-index: 2;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 10px; /* Reduced margin */
            border-bottom: 1px solid #333; /* Thinner border */
            padding-bottom: 5px; /* Reduced padding */
        }
        .invoice-header h1 {
            font-size: 20px; /* Smaller header */
            margin: 0;
            color: #333;
        }
        .invoice-header p {
            margin: 2px 0; /* Reduced margin */
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px; /* Reduced margin */
        }
        .invoice-info div {
            width: 48%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 4px; /* Reduced padding */
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary {
            margin-top: 10px; /* Reduced margin */
            text-align: right;
        }
        .footer {
            margin-top: 15px; /* Reduced margin */
            text-align: center;
            color: #666;
            font-size: 10px; /* Smaller font */
            border-top: 1px solid #ddd;
            padding-top: 5px; /* Reduced padding */
        }
        .bengali {
            font-family: 'SolaimanLipi', Arial, sans-serif;
        }
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }

            .custom-print-footer {
                display: block !important;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                text-align: center;
                color: #666;
                font-size: 10px;
                padding: 5px 0 2px 0;
                background: #fff;
                z-index: 9999;
            }
        }
        .custom-print-footer {
            display: none;
        }
        @page {
            margin: 0;
        }
        /* For Firefox only: show custom message and page number in footer */
        @page {
            /* Uncomment below for Firefox support */
            /* @bottom-center {
                content: "Thank you for your business! | Page " counter(page) " of " counter(pages);
            } */
        }
        .signature {
            margin-top: 35px; /* Reduced margin significantly */
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            width: 30%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 2px; /* Reduced padding */
        }

        .summary-flex-container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            align-items: flex-start;
        }

        .summary-table {
            width: 35%; /* Reduced width */
            margin-left: auto;
            border-collapse: collapse;
            margin-top: 0; /* Reset margin since using flex container */
        }

        .summary-table th, .summary-table td {
            padding: 3px; /* Further reduced padding */
            text-align: right;
            font-size: 13px; /* Smaller font size */
        }

        .summary-table th {
            font-weight: bold;
            width: 55%;
        }

        .notes-container {
            width: 60%;
            font-size: 10px;
            padding: 3px;
            align-self: flex-start;
        }

        /* Watermark */
        .watermark-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            opacity: 0.06;
        }
        .watermark-bg img {
            max-width: 60%;
            max-height: 60%;
        }
        .encodex-watermark{
            rotate: -45deg;
        }
        .text-end{
            text-align: right;
        }

        .btn-custom {
            border: 2px solid #000000; /* Button border */
            display: inline-block;
            padding: 3px 8px;
            background-color: #0f2d4a;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-custom:hover {
            background-color: #3751c7; /* hover এ একটু গাঢ় রঙ */
            transform: translateY(-1px); /* সামান্য উপরে উঠবে */
        }

        .btn-custom:active {
            transform: translateY(1px); /* চাপ দিলে নিচে নামবে */
        }

    </style>
    <!-- QRCode.js CDN -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <div class="invoice-container">
        <!-- Watermark inside invoice container -->
        <div class="watermark-bg">
            @if(get_setting('shop_logo'))
                <img src="{{ route('shop_logo.show', get_setting('shop_logo')) }}" alt="@lang('kazitds::kazitds.Shop Logo')">
            @else
                <img src="{{ asset('assets/img/default-img/Encodex_c.png') }}" alt="@lang('kazitds::kazitds.Encodex')" class="encodex-watermark">
            @endif
        </div>

        <div class="no-print" style="text-align: right; margin-bottom: 5px;">
            <a href="{{ route('sales.create') }}" class="btn-custom">@lang('kazitds::kazitds.Back')</a>
            <button onclick="window.print()" style="padding: 3px 8px; cursor: pointer; background: #0f9bd6; color: white; border-radius: 4px;">
                @lang('kazitds::kazitds.Print Invoice')
            </button>
        </div>

        <!-- Header: Left (Logo), Center (Info), Right (QR Code) -->
        <div class="invoice-header-flex">
            <div>
                @if(get_setting('shop_logo'))
                    <img src="{{ route('shop_logo.show', get_setting('shop_logo')) }}" class="header-logo" alt="@lang('kazitds::kazitds.Shop Logo')">
                @else
                    <img src="{{ asset('assets/img/default-img/Encodex_c.png') }}" class="header-logo" alt="@lang('kazitds::kazitds.Encodex')">
                @endif
            </div>
            <div class="header-info">
                <h1 style="margin:0; margin-bottom:1px; font-size:2.33rem; margin-top: 0.5rem">{{ get_setting('shop_name', config('app.name', 'ENcodeX')) }}</h1>
                <p style="margin:1px">{{ get_setting('shop_address', __('kazitds::kazitds.Your Company Address Here')) }}</p>
                <p style="margin:1px">
                    @lang('kazitds::kazitds.Phone'): {{ get_setting('shop_phone', '+880 123456789') }} |
                    @lang('kazitds::kazitds.Email'): {{ get_setting('shop_email', 'info@example.com') }}
                </p>
                <h2 style="margin:0; margin-top:2px; letter-spacing: 2px;">@lang('kazitds::kazitds.INVOICE')</h2>
            </div>
            <div class="header-qrcode">
                <div id="qrcode"></div>
            </div>
        </div>

        <div class="invoice-info">
            <div>
                <strong>@lang('kazitds::kazitds.Invoice To'):</strong>
                {{ $sale->customer_name ?? __('kazitds::kazitds.N/A') }}<br>
                @if($sale->mobile_number)
                    @lang('kazitds::kazitds.Phone'): {{ toBanglaPhone($sale->mobile_number) }}<br>
                @endif
                @if($sale->customer && $sale->customer->address)
                    @lang('kazitds::kazitds.Address'): {{ $sale->customer->address }}<br>
                @endif
            </div>
            <div style="text-align: right;">
                <strong>@lang('kazitds::kazitds.Invoice Number'):</strong> {{ ($sale->invoice_number) }}<br>
                <strong>@lang('kazitds::kazitds.Date'):</strong> {{ formatDateTime($sale->sale_date) }}<br>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">@lang('kazitds::kazitds.Product')</th>
                    <th class="text-center">@lang('kazitds::kazitds.Qty')</th>
                    <th class="text-center">@lang('kazitds::kazitds.Unit Price')</th>
                    @if(get_setting('show_discount_option', true))
                        <th class="text-center">@lang('kazitds::kazitds.Disc')</th>
                    @endif
                    <th class="text-center">@lang('kazitds::kazitds.Total')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ toBanglaNumber($loop->iteration) }}</td>
                    <td>
                        @if($item->productVariant->brand)
                            {{ $item->productVariant->brand->name }}
                        @endif
                        {{ $item->productVariant->product->name }} - <b>{{ $item->productVariant->pack->name }}</b>
                    </td>
                    <td class="text-center">{{ toBanglaNumber($item->quantity) }}</td>
                    <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($item->price_per_unit, 2) }}</td>
                    @if(get_setting('show_discount_option', true))
                        <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($item->item_discount, 2) }}</td>
                    @endif
                    <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-flex-container">
            <div class="notes-container">
                @if($sale->notes)
                    <div>
                        <strong>@lang('kazitds::kazitds.Notes'):</strong> {{ $sale->notes }}
                    </div>
                @endif
            </div>

            <table class="summary-table">
                <tr>
                    <th>@lang('kazitds::kazitds.Subtotal'):</th>
                    <td>@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($dues?->sale_amount ?? 0, 2) }}</td>
                </tr>
                @if($sale->discount > 0 && get_setting('show_discount_option', true))
                <tr>
                    <th>@lang('kazitds::kazitds.Discount'):</th>
                    <td>@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($sale->discount, 2) }}</td>
                </tr>
                @endif
                @if( get_setting('show_previous_due_option', true))
                <tr>
                    <th>@lang('kazitds::kazitds.Previous Due'):</th>
                    <td>@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($dues?->previous_due ?? 0, 2) }}</td>
                </tr>
                @endif
                <tr style="font-weight: bold;">
                    <th>@lang('kazitds::kazitds.Grand Total'):</th>
                    <td>@lang("kazitds::kazitds.TK.") {{ toBanglaNumber(($dues?->sale_amount ?? 0) + ($dues?->previous_due ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <th class="text-success">@lang('kazitds::kazitds.Payment Amount'):</th>
                    <td class="text-success">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($dues?->paid_amount ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-danger">@lang('kazitds::kazitds.Due After Payment'):</th>
                    <td class="text-danger fw-bold" style="font-weight: bold;">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber(($dues->total_due ?? 0), 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="signature mt-4" style="margin-left: 1.7rem !important; margin-right: 1.7rem !important;">
            <div>@lang('kazitds::kazitds.Received By')</div>
            <div>@lang('kazitds::kazitds.Authorized')</div>
        </div>

        <div class="footer">
            <p style="margin: 0;">
                <small>
                    @lang('kazitds::kazitds.This is a computer-generated invoice.') | @lang('kazitds::kazitds.Developed by: ENcodeX')
                </small>
            </p>
        </div>
        <div class="custom-print-footer"></div>
    </div>


    <script>
        window.addEventListener('DOMContentLoaded', function() {
            new QRCode(document.getElementById("qrcode"), {
                text: "{{ route('saleInvoice', $sale->invoice_number) }}",
                width: 90,
                height: 90,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        });
        window.print();
    </script>
</body>
</html>

