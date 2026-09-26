@extends('me::master')

@section('title', trans('kazitds::kazitds.SMS Log'))

@section('content')
@php $tk = __('kazitds::kazitds.TK.'); @endphp

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card glass-card h-100 p-3">
            <div class="small text-muted text-uppercase fw-semibold">@lang('me::me.local_balance')</div>
            <div class="h4 fw-bold mb-0 {{ $account->balance < $account->sms_rate ? 'text-danger' : 'text-success' }}">
                {{ $tk }} {{ toBanglaNumber($account->balance, 2) }}
            </div>
            <div class="small text-muted">@lang('me::me.total_recharged'): {{ $tk }} {{ toBanglaNumber($account->admin_recharge_amount, 2) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card glass-card h-100 p-3">
            <div class="small text-muted text-uppercase fw-semibold">@lang('me::me.sms_remaining')</div>
            <div class="h4 fw-bold mb-0 text-primary">{{ toBanglaNumber($account->smsRemaining()) }}</div>
            <div class="small text-muted">@lang('me::me.sms_rate'): {{ $tk }} {{ toBanglaNumber($account->sms_rate, 2) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card glass-card h-100 p-3">
            <div class="small text-muted text-uppercase fw-semibold">@lang('me::me.sent_this_month')</div>
            <div class="h4 fw-bold mb-0">{{ toBanglaNumber($sentThisMonth) }}</div>
            <div class="small text-muted">@lang('me::me.total_sent'): {{ toBanglaNumber($account->sms_used) }}</div>
        </div>
    </div>
</div>

<div class="card glass-card w-100">
    <form method="GET" action="{{ route('sms_log.index') }}" class="mb-3">
        <div class="row g-2">
            <div class="col-md">
                <input type="text" name="phone" class="form-control form-control-sm" placeholder="@lang('me::me.mobile_number')" value="{{ request('phone') }}">
            </div>
            <div class="col-md">
                <select name="status" class="form-select form-select-sm">
                    <option value="">@lang('me::me.all_statuses')</option>
                    @foreach (['success', 'failed', 'error'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>@lang('me::me.sms_status_' . $status)</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md">
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}" title="@lang('me::me.from_date')">
            </div>
            <div class="col-md">
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}" title="@lang('me::me.to_date')">
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-sm btn-encodex-search rounded"><i class="fas fa-search"></i> @lang('kazitds::kazitds.Search')</button>
                <a href="{{ route('sms_log.index') }}" class="btn btn-sm btn-encodex-clear rounded"><i class="fas fa-eraser"></i> @lang('kazitds::kazitds.Reset')</a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover table-striped table-encodex">
            <thead>
                <tr>
                    <th>@lang('me::me.sent_to')</th>
                    <th>@lang('me::me.message')</th>
                    <th>@lang('me::me.Status')</th>
                    <th>@lang('me::me.time')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="text-nowrap">{{ toBanglaPhone($log->to) }}</td>
                        <td style="white-space: pre-line; min-width: 260px">{{ $log->message }}</td>
                        <td>
                            <span class="badge {{ $log->status === 'success' ? 'bg-success' : 'bg-danger' }}">
                                <i class="fas fa-{{ $log->status === 'success' ? 'check' : 'times' }}"></i>
                                @lang('me::me.sms_status_' . $log->status)
                            </span>
                        </td>
                        <td class="text-nowrap">{{ formatDateTime($log->created_at) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">@lang('me::me.no_sms_logs_found')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $logs->links('pagination::bootstrap-5') }}
</div>
@endsection
