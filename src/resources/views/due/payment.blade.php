@extends('me::master')

@section('title', trans('kazitds::kazitds.Payments'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('due.index'),
      'text' => __('kazitds::kazitds.All Dues'),
      'class' => 'btn-encodex-list'
  ])
  @endcomponent
@endpush

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4 w-100">
            <div class="card-header bg-info text-white p-2">
                <h5 class="mb-0 d-flex justify-content-between align-items-center">
                    <span>@lang('kazitds::kazitds.Payment History') - {{ $customer->name }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0 table-encodex table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('kazitds::kazitds.Amount')</th>
                                <th>@lang('kazitds::kazitds.Payment Method')</th>
                                <th>@lang('kazitds::kazitds.Note')</th>
                                <th>@lang('kazitds::kazitds.Paid At')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentHistories as $history)
                                <tr>
                                    <td>{{ toBanglaNumber($loop->iteration) }}</td>
                                    <td>@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($history->amount, 2) }}</td>
                                    <td>{{ __($history->payment_method) ?? __('kazitds::kazitds.N/A') }}</td>
                                    <td>{{ $history->note ?? __('kazitds::kazitds.--') }}</td>
                                    <td>{{ formatDate($history->created_at) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">@lang('kazitds::kazitds.No payment history found.')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-2">
                        @if(method_exists($paymentHistories, 'links'))
                            {{ $paymentHistories->links('pagination::bootstrap-5') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header p-1 bg-encodex-secondary text-white">
                <h5 class="mb-0 d-flex justify-content-between align-items-center">
                    <span>@lang('kazitds::kazitds.Make Payment')</span>
                    <span class="badge bg-danger fs-6 py-2">
                        @lang('kazitds::kazitds.Due'): {{ toBanglaNumber($customer->due_amount ?? 0, 2) }}
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('due.payment.store', $customer->id) }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="amount">@lang('kazitds::kazitds.Amount')</label>
                        <input type="number" name="amount" id="amount" class="form-control form-control-sm" min="1" max="{{ $customer->due_amount }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="payment_method">@lang('kazitds::kazitds.Payment Method')</label>
                        <select name="payment_method" id="payment_method" class="form-select form-select-sm" data-control="select2">
                            <option value="Cash">@lang('kazitds::kazitds.Cash')</option>
                            <option value="Bkash">@lang('kazitds::kazitds.Bkash')</option>
                            <option value="Nagad">@lang('kazitds::kazitds.Nagad')</option>
                            <option value="Rocket">@lang('kazitds::kazitds.Rocket')</option>
                            <option value="Upay">@lang('kazitds::kazitds.Upay')</option>
                            <option value="Bank">@lang('kazitds::kazitds.Bank')</option>
                            <option value="Others">@lang('kazitds::kazitds.Others')</option>
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label for="note">@lang('kazitds::kazitds.Note')</label>
                        <textarea name="note" id="note" class="form-control" placeholder="Ex. Transaction ID, Bank Information" rows="1"></textarea>
                    </div>
                    <div class="form-group row mb-1">
                        <label for="sms" class="col-sm-4 col-form-label text-warning">@lang('kazitds::kazitds.Send SMS')</label>
                        <div class="col-sm-8">
                            <input type="checkbox" class="form-check-input" style="vertical-align: bottom; width:1.5rem !important; height:1.5rem !important" id="sms" name="sms" value="1" {{ old('sms', 0) ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="form-group row mt-3">
                        <div class="col-sm-9 offset-sm-3 text-end">
                            <button type="submit" class="btn btn-sm btn-encodex">
                                <i class="fa fa-money-bill-wave"></i> @lang('kazitds::kazitds.Pay')
                            </button>
                            {{-- <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-encodex-cancel">@lang('kazitds::kazitds.Cancel')</a> --}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

