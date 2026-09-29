@extends('me::master')

@section('title', trans('kazitds::kazitds.Sales'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('sales.create'),
      'text' => __('kazitds::kazitds.Create Sale'),
      'class' => 'btn-encodex-create'
  ])
  @endcomponent
@endpush

@section('content')
<div class="card shadow w-100">
    <div class="card-body">
        <!-- Search/Filter Form -->
        <form method="GET" action="{{ route('sales.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-3 mb-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                        </div>
                        <input type="text" name="invoice_number" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.Invoice Number')" value="{{ request('invoice_number') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-0">
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
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="date" name="start_date" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.Start Date')" value="{{ request('start_date') }}">
                    </div>
                </div>
                <div class="col-md-2 mb-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="date" name="end_date" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.End Date')" value="{{ request('end_date') }}">
                    </div>
                </div>
                <div class="col-md-2 mb-0">
                    <div class="btn-group" role="group">
                        <button type="submit" class="btn btn-sm btn-encodex-search rounded me-1">
                            <i class="fas fa-search"></i> @lang('kazitds::kazitds.Search')
                        </button>
                        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-encodex-clear rounded">
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
                        <th>@lang('kazitds::kazitds.Invoice')</th>
                        <th>@lang('kazitds::kazitds.Date')</th>
                        <th>@lang('kazitds::kazitds.Customer')</th>
                        <th class="text-center">@lang('kazitds::kazitds.Items')</th>
                        <th class="text-end">@lang('kazitds::kazitds.Total')</th>
                        @if(get_setting('show_discount_option'))
                            <th class="text-end">@lang('kazitds::kazitds.Discount')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Net Amount')</th>
                        @endif
                        <th width="120" class="text-center">@lang('kazitds::kazitds.Actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>{{ toBanglaNumber($loop->iteration) }}</td>
                            <td>
                                <span class="badge bg-info">{{ $sale->invoice_number }}
                                </span>
                                @if($sale->returns()->exists())
                                    <br><span class="badge bg-warning text-danger">@lang('kazitds::kazitds.Returned')</span>
                                @endif
                            </td>
                            <td>{{ formatDateTime($sale->sale_date) }}</td>
                            <td>
                                {{ $sale->customer_name ?? __('kazitds::kazitds.N/A') }}
                                @if($sale->customer_id && $sale->mobile_number)
                                    <br><small class="text-muted">{{ $sale->mobile_number }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary text-white">{{ toBanglaNumber($sale->items->count()) }}</span>
                            </td>
                            <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($sale->total_amount, 2) }}</td>
                            @if(get_setting('show_discount_option'))
                                <td class="text-end">
                                    @if($sale->discount > 0)
                                        <span class="text-danger">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($sale->discount, 2) }}</span>
                                    @else
                                        <span class="text-muted">@lang("kazitds::kazitds.TK.") 0.00</span>
                                    @endif
                                </td>
                                <td class="text-end font-weight-bold">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($sale->net_amount, 2) }}</td>
                            @endif
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center" style="">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-encodex-show btn-sm " title="@lang("kazitds::kazitds.View")">
                                        <i class="fas fa-eye "></i>
                                    </a>

                                    @if(!$sale->returns()->exists())
                                        <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-encodex-edit btn-sm " title="@lang("kazitds::kazitds.Edit")">
                                            <i class="fas fa-edit "></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('sales.print-invoice', $sale->id) }}" target="_blank" class="btn btn-encodex-print btn-sm " title="@lang("kazitds::kazitds.Print")">
                                        <i class="fas fa-print "></i>
                                    </a>

                                    @if(!$sale->returns()->exists())
                                        <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button title="@lang("kazitds::kazitds.Delete")" onclick="return confirm('{{ __('kazitds::kazitds.Are you sure you want to delete this?') }}')"
                                                type="submit" class="btn btn-sm btn-encodex-delete ">
                                                <i class="fas fa-trash "></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-search mr-2"></i> @lang('kazitds::kazitds.No sales found')
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->

            @if(method_exists($sales, 'links'))
            {{ $sales->links('pagination::bootstrap-5') }}
            @endif
        </div>
    </div>
</div>

@endsection

@push('css')
<style>

</style>
@endpush
