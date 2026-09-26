@extends('me::master')

@section('title', trans('kazitds::kazitds.Purchase Details'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('purchases.index'),
      'text' => __('kazitds::kazitds.All Purchases'),
      'class' => 'btn-encodex-list'
  ])
  @endcomponent
@endpush

@section('content')
<div class="card shadow mb-4 w-100">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover mb-0">
                <tbody>
                    <tr>
                        <th width="200">@lang('kazitds::kazitds.Purchase Number')</th>
                        <td>{{ $purchase->purchase_number }}</td>
                        <th>@lang('kazitds::kazitds.Purchase Date')</th>
                        <td>{{ formatDate($purchase->purchase_date) }}</td>
                    </tr>
                    <tr>
                        <th>@lang('kazitds::kazitds.Supplier')</th>
                        <td colspan="3">
                            {{ $purchase->supplier->name ?? __('kazitds::kazitds.N/A') }}
                            @if($purchase->supplier && $purchase->supplier->company_name)
                                ({{ $purchase->supplier->company_name }})
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('kazitds::kazitds.Product')</th>
                        <td>{{ $purchase->productVariant->product->name ?? __('kazitds::kazitds.N/A') }}</td>
                        <th>@lang('kazitds::kazitds.Brand')</th>
                        <td>{{ $purchase->productVariant->brand->name ?? '--' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('kazitds::kazitds.Pack Size')</th>
                        <td>{{ $purchase->productVariant->pack->name ?? __('kazitds::kazitds.N/A') }}</td>
                        <th>@lang('kazitds::kazitds.Quantity')</th>
                        <td>{{ toBanglaNumber($purchase->quantity) }}</td>
                    </tr>
                    <tr>
                        <th>@lang('kazitds::kazitds.Price Per Unit')</th>
                        <td>@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($purchase->price_per_unit, 2) }}</td>
                        <th>@lang('kazitds::kazitds.Total Price')</th>
                        <td>@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($purchase->total_price, 2) }}</td>
                    </tr>
                    <tr>
                        <th>@lang('kazitds::kazitds.Created By')</th>
                        <td>{{ $purchase->creator->name ?? __('kazitds::kazitds.N/A') }}</td>
                        <th>@lang('kazitds::kazitds.Created At')</th>
                        <td>{{ formatDate($purchase->created_at) }}</td>
                    </tr>
                    @if($purchase->updated_by)
                        <tr>
                            <th>@lang('kazitds::kazitds.Updated By')</th>
                            <td>{{ $purchase->updater->name ?? __('kazitds::kazitds.N/A') }}</td>
                            <th>@lang('kazitds::kazitds.Updated At')</th>
                            <td>{{ formatDate($purchase->updated_at) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection
