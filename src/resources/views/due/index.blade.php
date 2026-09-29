@extends('me::master')

@section('title', trans('kazitds::kazitds.Due Collection'))

@section('content')
@php $tk = __('kazitds::kazitds.TK.'); @endphp

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card glass-card h-100 p-3">
            <div class="small text-muted text-uppercase fw-semibold">@lang('kazitds::kazitds.Total Due Receivable')</div>
            <div class="h4 fw-bold mb-0 text-danger">{{ $tk }} {{ toBanglaNumber($totalDue, 2) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card glass-card h-100 p-3">
            <div class="small text-muted text-uppercase fw-semibold">@lang('kazitds::kazitds.Customers with due')</div>
            <div class="h4 fw-bold mb-0">{{ toBanglaNumber($dueCustomerCount) }}</div>
        </div>
    </div>
    @if($isFiltered)
        <div class="col-md-4">
            <div class="card glass-card h-100 p-3">
                <div class="small text-muted text-uppercase fw-semibold">@lang('kazitds::kazitds.Due in this search')</div>
                <div class="h4 fw-bold mb-0 text-primary">{{ $tk }} {{ toBanglaNumber($filteredDue, 2) }}</div>
                <div class="small text-muted">@lang('kazitds::kazitds.Customers'): {{ toBanglaNumber($customers->total()) }}</div>
            </div>
        </div>
    @endif
</div>

<div class="card shadow w-100">
    <div class="card-body">
        <!-- Search/Filter Form -->
        <form method="GET" action="{{ route('due.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-2 mb-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" name="customer_name" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.Customer Name')" value="{{ request('customer_name') }}">
                    </div>
                </div>
                <div class="col-md-2 mb-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        </div>
                        <input type="text" name="customer_phone" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.Customer Phone')" value="{{ request('customer_phone') }}">
                    </div>
                </div>
                <div class="col-md-2 mb-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-arrow-down"></i></span>
                        </div>
                        <input type="text" name="min_due" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.Min Due')" value="{{ request('min_due') }}">
                    </div>
                </div>
                <div class="col-md-2 mb-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-arrow-up"></i></span>
                        </div>
                        <input type="text" name="max_due" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.Max Due')" value="{{ request('max_due') }}">
                    </div>
                </div>
                <div class="col-md-2 mb-0">
                    <div class="btn-group" role="group">
                        <button type="submit" class="btn btn-sm btn-encodex-search rounded me-1">
                            <i class="fas fa-search"></i> @lang('kazitds::kazitds.Search')
                        </button>
                        <a href="{{ route('due.index') }}" class="btn btn-sm btn-encodex-clear rounded">
                            <i class="fas fa-eraser"></i> @lang('kazitds::kazitds.Reset')
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Sales Table -->
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover table-encodex" width="100%" cellspacing="0">
                <thead class="">
                    <tr>
                        <th width="50">#</th>
                        <th>@lang('kazitds::kazitds.Customer Name')</th>
                        <th>@lang('kazitds::kazitds.Customer Phone')</th>
                        <th>@lang('kazitds::kazitds.Due Amount')</th>
                        <th width="120" class="text-center">@lang('kazitds::kazitds.Actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>{{ toBanglaNumber($customers->firstItem() + $loop->index) }}</td>
                            <td>
                                {{ $customer->name }} <a href="{{ route('customers.edit', $customer->id) }}"><i class="fas fa-edit"></i></a>
                            </td>
                            <td>{{ ($customer->phone) }}</td>
                            <td class="text-end">
                                @lang("kazitds::kazitds.TK.") {{ toBanglaNumber($customer->due_amount) ?? toBanglaNumber(0, 2) }}
                            </td>

                            <td class="text-center">
                                <div class="d-inline-flex align-items-center" style="">
                                    <a href="{{ route('due.payment.store', $customer->id) }}" class="btn btn-encodex-payment btn-sm " title="@lang("kazitds::kazitds.Payment")">
                                        <i class="fas fa-money-bill-wave "></i>
                                    </a>
                                    @if(isset(get_setting('sms_permit')['reminder']) && get_setting('sms_permit')['reminder'] == 1)
                                        <form action="{{ route('due.notify.send', $customer->id) }}" method="POST" class="d-inline-flex"
                                            onsubmit="return confirm('{{ __('kazitds::kazitds.Are you sure you want to send this reminder?') }}')">
                                            @csrf
                                            <button type="submit" class="btn btn-encodex-print btn-sm " title="@lang('kazitds::kazitds.Reminder')">
                                                <i class="fas fa-bell "></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-search mr-2"></i> @lang('kazitds::kazitds.No Due found')
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($customers->total() > 0)
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">@lang('kazitds::kazitds.Total Due')</td>
                            <td class="text-end text-danger">{{ $tk }} {{ toBanglaNumber($filteredDue, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>

            <!-- Pagination -->


            @if(method_exists($customers, 'links'))
                {{ $customers->links('pagination::bootstrap-5') }}
            @endif
        </div>
    </div>
</div>

@endsection

