@extends('me::master')

@section('title', trans('kazitds::kazitds.Sales Report'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('sales.reports.print') . '?' . http_build_query(request()->query()),
      'text' => __('kazitds::kazitds.Print'),
      'bg' => 'info',
      'icon' => 'print',
      'attribute' => 'target="_blank"',
      'class' => 'btn-encodex-print2'
  ])
  @endcomponent
@endpush

@section('content')
@php $tk = __('kazitds::kazitds.TK.'); @endphp

<div class="card shadow mb-4 w-100">
    <div class="card-body">
        <form method="GET" action="{{ route('sales.reports') }}">
            <input type="hidden" name="view" value="{{ $view }}">
            <div class="row g-2 mb-3">
                <div class="col-md-2">
                    <input type="date" name="start_date" title="@lang('kazitds::kazitds.Start Date')" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="end_date" title="@lang('kazitds::kazitds.End Date')" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <select name="customer_id" class="form-select form-select-sm" data-control="select2">
                        <option value="">@lang('kazitds::kazitds.All Customers')</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="product_variant_id" class="form-select form-select-sm" data-control="select2">
                        <option value="">@lang('kazitds::kazitds.All Products')</option>
                        @foreach($productVariants as $variant)
                            <option value="{{ $variant->id }}" {{ request('product_variant_id') == $variant->id ? 'selected' : '' }}>
                                {{ $variant->product->name }} - {{ $variant->brand->name ?? '--' }} - {{ $variant->pack->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <input type="text" name="invoice_number" class="form-control form-control-sm" placeholder="# @lang('kazitds::kazitds.Invoice')" value="{{ request('invoice_number') }}">
                </div>
                <div class="col-md-2 d-flex">
                    <button type="submit" class="btn btn-sm btn-encodex-search me-1">
                        <i class="fas fa-search me-1"></i> @lang('kazitds::kazitds.Search')
                    </button>
                    <a href="{{ route('sales.reports', ['view' => $view]) }}" class="btn btn-sm btn-encodex-clear">
                        <i class="fas fa-eraser me-1"></i> @lang('kazitds::kazitds.Reset')
                    </a>
                </div>
            </div>
        </form>

        {{-- View toggle + summary --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'product', 'page' => null]) }}" class="btn {{ $view === 'product' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-boxes me-1"></i> @lang('kazitds::kazitds.Product-wise')
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'list', 'page' => null]) }}" class="btn {{ $view === 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-file-invoice me-1"></i> @lang('kazitds::kazitds.Invoice-wise')
                </a>
            </div>
            <div class="small text-muted">
                @lang('kazitds::kazitds.Invoices'): <strong>{{ toBanglaNumber($totals->invoices) }}</strong>
                · @lang('kazitds::kazitds.Quantity'): <strong>{{ toBanglaNumber($itemTotals->quantity) }}</strong>
                · @lang('kazitds::kazitds.Net Sales'): <strong>{{ $tk }} {{ toBanglaNumber($totals->net, 2) }}</strong>
            </div>
        </div>

        <div class="table-responsive">
            @if($view === 'product')
                <table class="table table-bordered table-sm table-hover table-encodex">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('kazitds::kazitds.Product')</th>
                            <th>@lang('kazitds::kazitds.Brand')</th>
                            <th>@lang('kazitds::kazitds.Pack')</th>
                            <th class="text-center">@lang('kazitds::kazitds.Invoices')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Quantity')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Avg. Price')</th>
                            <th class="text-end">@lang('kazitds::kazitds.Total')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td>{{ toBanglaNumber($rows->firstItem() + $loop->index) }}</td>
                                <td><strong>{{ $row->variant->product->name ?? __('kazitds::kazitds.N/A') }}</strong></td>
                                <td>{{ $row->variant->brand->name ?? '--' }}</td>
                                <td>{{ $row->variant->pack->name ?? '--' }}</td>
                                <td class="text-center"><span class="badge bg-secondary">{{ toBanglaNumber($row->invoice_count) }}</span></td>
                                <td class="text-end">{{ toBanglaNumber($row->total_quantity) }}</td>
                                <td class="text-end">{{ $tk }} {{ toBanglaNumber($row->avg_price, 2) }}</td>
                                <td class="text-end fw-bold">{{ $tk }} {{ toBanglaNumber($row->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-3 text-muted"><i class="fas fa-info-circle me-1"></i> @lang('kazitds::kazitds.No data available')</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">@lang('kazitds::kazitds.Grand Total')</td>
                            <td class="text-end">{{ toBanglaNumber($itemTotals->quantity) }}</td>
                            <td></td>
                            <td class="text-end">{{ $tk }} {{ toBanglaNumber($itemTotals->amount, 2) }}</td>
                        </tr>
                        {{-- Invoice-level discount can't be split per product; show it once, when the report isn't narrowed to one product --}}
                        @if(!request('product_variant_id') && $totals->discount > 0)
                            <tr class="text-danger">
                                <td colspan="7" class="text-end">(-) @lang('kazitds::kazitds.Invoice Discount')</td>
                                <td class="text-end">{{ $tk }} {{ toBanglaNumber($totals->discount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-end">@lang('kazitds::kazitds.Net Sales')</td>
                                <td class="text-end">{{ $tk }} {{ toBanglaNumber($totals->net, 2) }}</td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            @else
                <table class="table table-bordered table-sm table-hover table-encodex">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('kazitds::kazitds.Invoice Number')</th>
                            <th>@lang('kazitds::kazitds.Date')</th>
                            <th>@lang('kazitds::kazitds.Customer')</th>
                            <th class="text-center">@lang('kazitds::kazitds.Items')</th>
                            @if(get_setting('show_discount_option'))
                                <th class="text-end">@lang('kazitds::kazitds.Subtotal')</th>
                                <th class="text-end">@lang('kazitds::kazitds.Discount')</th>
                            @endif
                            <th class="text-end">@lang('kazitds::kazitds.Total')</th>
                            <th class="text-center">@lang('kazitds::kazitds.Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $sale)
                            <tr>
                                <td>{{ toBanglaNumber($rows->firstItem() + $loop->index) }}</td>
                                <td><span class="badge bg-info">{{ $sale->invoice_number }}</span></td>
                                <td>{{ formatDate($sale->sale_date) }}</td>
                                <td>
                                    @if($sale->customer_id)
                                        <strong>{{ $sale->customer_name }}</strong>
                                        @if($sale->mobile_number)
                                            <br><small class="text-muted">{{ $sale->mobile_number }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">@lang('kazitds::kazitds.--')</span>
                                    @endif
                                </td>
                                <td class="text-center"><span class="badge bg-secondary">{{ toBanglaNumber($sale->items->count()) }}</span></td>
                                @if(get_setting('show_discount_option'))
                                    <td class="text-end">{{ $tk }} {{ toBanglaNumber($sale->total_amount, 2) }}</td>
                                    <td class="text-end {{ $sale->discount > 0 ? 'text-danger' : 'text-muted' }}">{{ $tk }} {{ toBanglaNumber($sale->discount, 2) }}</td>
                                @endif
                                <td class="text-end fw-bold">{{ $tk }} {{ toBanglaNumber($sale->net_amount, 2) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-encodex-show" title="@lang('kazitds::kazitds.View Details')"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('sales.print-invoice', $sale->id) }}" target="_blank" class="btn btn-sm btn-encodex-print" title="@lang('kazitds::kazitds.Print Invoice')"><i class="fas fa-print"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center py-3 text-muted"><i class="fas fa-info-circle me-1"></i> @lang('kazitds::kazitds.No data available')</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">@lang('kazitds::kazitds.Grand Total')</td>
                            @if(get_setting('show_discount_option'))
                                <td class="text-end">{{ $tk }} {{ toBanglaNumber($totals->subtotal, 2) }}</td>
                                <td class="text-end text-danger">{{ $tk }} {{ toBanglaNumber($totals->discount, 2) }}</td>
                            @endif
                            <td class="text-end">{{ $tk }} {{ toBanglaNumber($totals->net, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @endif

            {{ $rows->links('pagination::bootstrap-5') }}
        </div>
        <p class="small text-muted mb-0"><em>@lang('kazitds::kazitds.Note: Sales with returns are excluded from this report.')</em></p>
    </div>
</div>
@endsection
