{{-- Shared report heading for printMaster2: shop logo + details, report title, extra lines (period, supplier, ...) --}}
<div class="print-header">
    <div class="company-info">
        @if(get_setting('shop_logo'))
            <img src="{{ route('shop_logo.show', get_setting('shop_logo')) }}" class="company-logo" alt="{{ get_setting('shop_name', config('app.name')) }}">
        @else
            <span class="company-logo"></span>
        @endif
        <div class="text-center" style="flex: 1">
            <div class="company-name">{{ get_setting('shop_name', config('app.name')) }}</div>
            @if(get_setting('shop_address'))
                <div class="company-address">{{ get_setting('shop_address') }}</div>
            @endif
            <div class="company-contact">
                @lang('kazitds::kazitds.Phone'): {{ get_setting('shop_phone', '') }} | @lang('kazitds::kazitds.Email'): {{ get_setting('shop_email', '') }}
            </div>
        </div>
        {{-- keeps the shop details centred opposite the logo --}}
        <span class="company-logo"></span>
    </div>
    <div class="report-title">{{ $reportTitle }}</div>
    @foreach (array_filter($lines ?? []) as $line)
        <div class="report-meta">{{ $line }}</div>
    @endforeach
</div>
