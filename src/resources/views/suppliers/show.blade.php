@extends('me::master')

@section('title', trans('kazitds::kazitds.Supplier Details'))

@push('buttons')
    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-encodex">
        <i class="fas fa-edit"></i> @lang('kazitds::kazitds.Edit')
    </a>
    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-list"></i> @lang('kazitds::kazitds.All Suppliers')
    </a>
@endpush

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">@lang('kazitds::kazitds.Supplier Information')</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th width="200">@lang('kazitds::kazitds.Name')</th>
                    <td>{{ $supplier->name }}</td>
                </tr>
                <tr>
                    <th>@lang('kazitds::kazitds.Company Name')</th>
                    <td>{{ $supplier->company_name ?? __('kazitds::kazitds.N/A') }}</td>
                </tr>
                <tr>
                    <th>@lang('kazitds::kazitds.Phone')</th>
                    <td>{{ $supplier->phone ?? __('kazitds::kazitds.N/A') }}</td>
                </tr>
                <tr>
                    <th>@lang('kazitds::kazitds.Email')</th>
                    <td>{{ $supplier->email ?? __('kazitds::kazitds.N/A') }}</td>
                </tr>
                <tr>
                    <th>@lang('kazitds::kazitds.Address')</th>
                    <td>{{ $supplier->address ?? __('kazitds::kazitds.N/A') }}</td>
                </tr>
                <tr>
                    <th>@lang('kazitds::kazitds.Notes')</th>
                    <td>{{ $supplier->notes ?? __('kazitds::kazitds.N/A') }}</td>
                </tr>
                <tr>
                    <th>@lang('kazitds::kazitds.Created At')</th>
                    <td>{{ $supplier->created_at->format('d M Y H:i:s') }}</td>
                </tr>
                <tr>
                    <th>@lang('kazitds::kazitds.Updated At')</th>
                    <td>{{ $supplier->updated_at->format('d M Y H:i:s') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">@lang('kazitds::kazitds.Recent Purchases')</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>@lang('kazitds::kazitds.Purchase #')</th>
                        <th>@lang('kazitds::kazitds.Date')</th>
                        <th>@lang('kazitds::kazitds.Product')</th>
                        <th>@lang('kazitds::kazitds.Quantity')</th>
                        <th>@lang('kazitds::kazitds.Total')</th>
                        <th>@lang('kazitds::kazitds.Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supplier->purchases()->latest()->take(5)->get() as $purchase)
                    <tr>
                        <td>{{ $purchase->purchase_number }}</td>
                        <td>{{ $purchase->purchase_date }}</td>
                        <td>{{ $purchase->productVariant->product->name }} - {{ $purchase->productVariant->brand->name ?? '--' }}</td>
                        <td>{{ $purchase->quantity }}</td>
                        <td>TK. {{ toBanglaNumber($purchase->total_price, 2) }}</td>
                        <td>
                            <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">@lang('kazitds::kazitds.No purchases found')</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <a href="{{ route('purchases.index', ['supplier_id' => $supplier->id]) }}" class="btn btn-sm btn-encodex">
                @lang('kazitds::kazitds.View All Purchases')
            </a>
        </div>
    </div>
</div>
@endsection
