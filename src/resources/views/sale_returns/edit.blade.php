@extends('me::master')

@section('title', __('kazitds::kazitds.Edit Sale Return'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('kazitds::kazitds.Edit Sale Return') #{{ $saleReturn->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('sale-returns.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> @lang('kazitds::kazitds.Back to List')
                        </a>
                    </div>
                </div>
                <form action="{{ route('sale-returns.update', $saleReturn) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sale_id">@lang('kazitds::kazitds.Sale *')</label>
                                    <select class="form-control @error('sale_id') is-invalid @enderror" id="sale_id" name="sale_id" required>
                                        <option value="">@lang('kazitds::kazitds.Select Sale')</option>
                                        @foreach($sales as $sale)
                                        <option value="{{ $sale->id }}" {{ old('sale_id', $saleReturn->sale_id) == $sale->id ? 'selected' : '' }}>
                                            #{{ $sale->id }} - {{ $sale->customer->name ?? '--' }} ({{ $sale->sale_date->format('d M Y') }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('sale_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="return_date">@lang('kazitds::kazitds.Return Date *')</label>
                                    <input type="date" class="form-control @error('return_date') is-invalid @enderror"
                                           id="return_date" name="return_date" value="{{ old('return_date', $saleReturn->return_date->format('Y-m-d')) }}" required>
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
                                           id="total_amount" name="total_amount" value="{{ old('total_amount', $saleReturn->total_amount) }}"
                                           step="0.01" min="0" required>
                                    @error('total_amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">@lang('kazitds::kazitds.Status')</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="pending" {{ old('status', $saleReturn->status) == 'pending' ? 'selected' : '' }}>@lang('kazitds::kazitds.Pending')</option>
                                        <option value="approved" {{ old('status', $saleReturn->status) == 'approved' ? 'selected' : '' }}>@lang('kazitds::kazitds.Approved')</option>
                                        <option value="rejected" {{ old('status', $saleReturn->status) == 'rejected' ? 'selected' : '' }}>@lang('kazitds::kazitds.Rejected')</option>
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
                                      id="reason" name="reason" rows="3" placeholder="@lang('kazitds::kazitds.Enter reason for return')">{{ old('reason', $saleReturn->reason) }}</textarea>
                            @error('reason')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">@lang('kazitds::kazitds.Notes')</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3" placeholder="@lang('kazitds::kazitds.Additional notes')">{{ old('notes', $saleReturn->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-encodex">
                            <i class="fas fa-save"></i> @lang('kazitds::kazitds.Update Sale Return')
                        </button>
                        <a href="{{ route('sale-returns.index') }}" class="btn btn-encodex-cancel">
                            <i class="fas fa-times"></i> @lang('kazitds::kazitds.Cancel')
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
