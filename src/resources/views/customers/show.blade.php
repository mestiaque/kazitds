@extends('me::master')

@section('title', trans('kazitds::kazitds.Customer Transaction History'))

@push('buttons')
    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-encodex-list">
        <i class="fas fa-arrow-left"></i> @lang('kazitds::kazitds.Customers')
    </a>
@endpush

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white py-2">
            <h5 class="mb-0">
                <i class="fas fa-user-circle me-1"></i>
                {{ $customer->name }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">@lang('kazitds::kazitds.Phone')</div>
                        <div>{{ $customer->phone ?: __('kazitds::kazitds.N/A') }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">@lang('kazitds::kazitds.Email')</div>
                        <div>{{ $customer->email ?: __('kazitds::kazitds.N/A') }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">@lang('kazitds::kazitds.Address')</div>
                        <div>{{ $customer->address ?: __('kazitds::kazitds.N/A') }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-left-primary h-100 mb-0">
                        <div class="card-body py-3">
                            <div class="text-muted small">@lang('kazitds::kazitds.Total Sale Amount')</div>
                            <div class="fs-5 fw-bold">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($totalDebit, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-success h-100 mb-0">
                        <div class="card-body py-3">
                            <div class="text-muted small">@lang('kazitds::kazitds.Total Paid Amount')</div>
                            <div class="fs-5 fw-bold text-success">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($totalCredit, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-info h-100 mb-0">
                        <div class="card-body py-3">
                            <div class="text-muted small">@lang('kazitds::kazitds.Current Due')</div>
                            <div class="fs-5 fw-bold">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($customer->due_amount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="mb-3">
                <i class="fas fa-list-alt me-1"></i>
                @lang('kazitds::kazitds.Transaction History')
            </h6>

            @if ($dueTrackingStartedAt)
                <div class="alert alert-info py-2 small">
                    <i class="fas fa-info-circle me-1"></i>
                    @lang('kazitds::kazitds.sales_before_due_tracking_note', ['date' => formatDateTime($dueTrackingStartedAt)])
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped table-hover table-encodex mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>#</th>
                            <th>@lang('kazitds::kazitds.Date')</th>
                            <th>@lang('kazitds::kazitds.Details')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Sale Amount')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Paid Amount')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Due')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ toBanglaNumber($transactions->firstItem() + $loop->index) }}</td>
                                <td class="text-nowrap">{{ formatDateTime($transaction['date']) }}</td>
                                <td>
                                    @if ($transaction['type'] === 'sale' && $transaction['sale_id'])
                                        <a href="{{ route('sales.show', $transaction['sale_id']) }}">
                                            {{ $transaction['details'] }}
                                        </a>
                                    @else
                                        {{ $transaction['details'] }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($transaction['debit'] > 0)
                                        @lang('kazitds::kazitds.TK.') {{ toBanglaNumber($transaction['debit'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($transaction['credit'] > 0)
                                        @lang('kazitds::kazitds.TK.') {{ toBanglaNumber($transaction['credit'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end fw-bold">
                                    @lang('kazitds::kazitds.TK.') {{ toBanglaNumber($transaction['balance'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    @lang('kazitds::kazitds.No transaction history found.')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="mt-3">
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
