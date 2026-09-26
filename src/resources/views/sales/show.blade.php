@extends('me::master')

@section('title', trans('kazitds::kazitds.Sale Details'))

@push('buttons')
  @if(!$sale->returns()->exists())
  @component('me::components.btn.add-button', [
      'route' => route('sales.edit', $sale->id),
      'text' => __('kazitds::kazitds.Edit'),
      'icon' => 'edit',
      'class' => 'btn-encodex-edit'
  ])
  @endcomponent
  @endif
  @component('me::components.btn.add-button', [
      'route' => route('sales.print-invoice', $sale->id),
      'text' => __('kazitds::kazitds.Print'),
      'bg' => 'info',
      'icon' => 'print',
      'attributes' => 'target="_blank"',
      'class' => 'btn-encodex-print2'
  ])
  @endcomponent
  @component('me::components.btn.add-button', [
      'route' => route('sales.index'),
      'text' => __('kazitds::kazitds.Sales List'),
      'icon' => 'list',
      'class' => 'btn-encodex-list '
  ])
  @endcomponent
@endpush

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-2 bg-primary">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-file-invoice me-1"></i>
                    @lang('kazitds::kazitds.Invoice'): {{ $sale->invoice_number }}
                </h5>
            </div>
            <div class="col-auto text-end">
                <span class="badge bg-light text-dark">
                    @lang('kazitds::kazitds.Date'): {{ formatDateTime($sale->sale_date) }}
                </span>
            </div>
        </div>
    </div>

    <div class="card-body">
        @if($sale->returns()->exists())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> This sale has returns associated with it and cannot be edited or deleted.
        </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="customer-info mb-0">
                    <h6 class="font-weight-bold text-primary mb-3 border-bottom pb-2">
                        <i class="fas fa-user-circle me-1"></i> @lang('kazitds::kazitds.Customer'):
                        <span class="text-dark">
                            @if($sale->customer)
                                &nbsp; {{ $sale->customer_name ?? '--' }} - {{ $sale->mobile_number ?? '--' }}
                                @if($sale->customer->address)
                                    &nbsp; &nbsp; <small><strong>@lang('kazitds::kazitds.Address'):</strong> {{ $sale->customer->address ?? '--' }}</small>
                                @endif
                            @else
                                @lang("kazitds::kazitds.N/A")
                            @endif
                        </span>
                    </h6>
                </div>
            </div>
        </div>

        <h6 class="font-weight-bold text-primary mb-3 border-bottom pb-2">
            <i class="fas fa-shopping-cart me-1"></i> @lang('kazitds::kazitds.Sale Items')
        </h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover table-striped table-encodex">
                <thead class="">
                    <tr>
                        <th>#</th>
                        <th>@lang('kazitds::kazitds.Product')</th>
                        <th>@lang('kazitds::kazitds.Brand')</th>
                        <th>@lang('kazitds::kazitds.Pack')</th>
                        <th class="text-center">@lang('kazitds::kazitds.Quantity')</th>
                        <th class="text-end">@lang('kazitds::kazitds.Price')</th>
                        @if(get_setting('show_discount_option', true))
                        <th class="text-end">@lang('kazitds::kazitds.Discount')</th>
                        @endif
                        <th class="text-end">@lang('kazitds::kazitds.Total')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr>
                        <td>{{ toBanglaNumber($loop->iteration) }}</td>
                        <td><strong>{{ $item->productVariant->product->name }}</strong></td>
                        <td>{{ $item->productVariant->brand->name ?? '--' }}</td>
                        <td>{{ $item->productVariant->pack->name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($item->price_per_unit, 2) }}</td>
                        @if(get_setting('show_discount_option', true))
                        <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($item->item_discount, 2) }}</td>
                        @endif
                        <td class="text-end font-weight-bold">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($item->total_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
        <!-- Sale Summary Section (matches form style/colors) -->
        <div class="row mt-2">
            <div class="col-md-6">
                <strong>@lang("kazitds::kazitds.Note") :</strong> {{ $sale->notes }}
            </div>
            <div class="col-md-6">
                <table class="table table-bordered table-sm mb-0 border border-primary">
                    <tbody>
                        <tr>
                            <th class="text-start">@lang('kazitds::kazitds.Subtotal')</th>
                            <td class="text-end">
                                @lang("kazitds::kazitds.TK.") {{ toBanglaNumber($dues?->sale_amount, 2) }}
                            </td>
                        </tr>
                        @if($sale->discount > 0 && get_setting('show_discount_option', true))
                        <tr>
                            <th class="text-start">@lang('kazitds::kazitds.Discount')</th>
                            <td class="text-end text-danger">
                                @lang("kazitds::kazitds.TK.") {{ toBanglaNumber($sale->discount, 2) }}
                            </td>
                        </tr>
                        @endif
                        @if(get_setting('show_previous_due_option', true))
                        <tr>
                            <th class="text-start text-encodex-secondary">@lang('kazitds::kazitds.Previous Due')</th>
                            <td class="text-end text-encodex-secondary">
                                @lang("kazitds::kazitds.TK.") {{ toBanglaNumber($dues?->previous_due, 2) }}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th class="text-start fw-bold">@lang('kazitds::kazitds.Grand Total')</th>
                            <td class="text-end fw-bold">
                                @lang("kazitds::kazitds.TK.") {{ toBanglaNumber(($dues?->sale_amount ?? 0) + ($dues?->previous_due ?? 0), 2) }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-start text-success">@lang('kazitds::kazitds.Payment Amount')</th>
                            <td class="text-end text-success">
                                @lang("kazitds::kazitds.TK.") {{ toBanglaNumber($dues->paid_amount ?? 0, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-start text-danger">@lang('kazitds::kazitds.Due After Payment')</th>
                            <td class="text-end text-danger fw-bold">
                                @lang("kazitds::kazitds.TK.") {{ toBanglaNumber(($dues->total_due ?? 0), 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .thead-dark th {
        background-color: #343a40;
        color: white;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.075);
    }
</style>
@endpush
