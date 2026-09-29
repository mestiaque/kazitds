@extends('me::master')

@section('title', __('kazitds::kazitds.Edit Purchase Return'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('kazitds::kazitds.Edit Purchase Return') #{{ $purchaseReturn->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('purchase-returns.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> @lang('kazitds::kazitds.Back to List')
                        </a>
                    </div>
                </div>
                <form action="{{ route('purchase-returns.update', $purchaseReturn) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_id">@lang('kazitds::kazitds.Purchase *')</label>
                                    <select class="form-control @error('purchase_id') is-invalid @enderror" id="purchase_id" name="purchase_id" required>
                                        <option value="">@lang('kazitds::kazitds.Select Purchase')</option>
                                        @foreach($purchases as $purchase)
                                        <option value="{{ $purchase->id }}" {{ old('purchase_id', $purchaseReturn->purchase_id) == $purchase->id ? 'selected' : '' }}>
                                            #{{ $purchase->id }} - {{ $purchase->supplier->name ?? __('kazitds::kazitds.N/A') }} ({{ $purchase->purchase_date->format('d M Y') }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('purchase_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="return_date">@lang('kazitds::kazitds.Return Date *')</label>
                                    <input type="date" class="form-control @error('return_date') is-invalid @enderror"
                                           id="return_date" name="return_date" value="{{ old('return_date', $purchaseReturn->return_date->format('Y-m-d')) }}" required>
                                    @error('return_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_amount">@lang('kazitds::kazitds.Total Return Amount *')</label>
                                    <input type="number" class="form-control @error('total_amount') is-invalid @enderror"
                                           id="total_amount" name="total_amount" value="{{ old('total_amount', $purchaseReturn->total_amount) }}"
                                           step="0.01" min="0" required>
                                    @error('total_amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="pending" {{ old('status', $purchaseReturn->status) == 'pending' ? 'selected' : '' }}>@lang('kazitds::kazitds.Pending')</option>
                                        <option value="approved" {{ old('status', $purchaseReturn->status) == 'approved' ? 'selected' : '' }}>@lang('kazitds::kazitds.Approved')</option>
                                        <option value="rejected" {{ old('status', $purchaseReturn->status) == 'rejected' ? 'selected' : '' }}>@lang('kazitds::kazitds.Rejected')</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reason">@lang('kazitds::kazitds.Reason for Return')</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror"
                                      id="reason" name="reason" rows="3" placeholder="@lang('kazitds::kazitds.Enter reason for return')">{{ old('reason', $purchaseReturn->reason) }}</textarea>
                            @error('reason')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3" placeholder="@lang('kazitds::kazitds.Additional notes')">{{ old('notes', $purchaseReturn->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-encodex">
                            <i class="fas fa-save"></i> @lang('kazitds::kazitds.Update Purchase Return')
                        </button>
                        <a href="{{ route('purchase-returns.index') }}" class="btn btn-encodex-cancel">
                            <i class="fas fa-times"></i> @lang('kazitds::kazitds.Cancel')
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
