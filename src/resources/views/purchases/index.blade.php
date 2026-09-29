@extends('me::master')

@section('title', trans('kazitds::kazitds.Purchases'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('purchases.create'),
      'text' => __('kazitds::kazitds.Add Purchase'),
      'class' => 'btn-encodex-create'
  ])
  @endcomponent
@endpush

@section('content')
  <div class="card border-left-primary shadow h-100 w-100">
    <div class="card-body">
      <form method="GET" action="{{ route('purchases.index') }}" class="mb-3">
        <div class="row">
          <div class="col-md-4">
            <select name="product_variant" class="form-control form-control-sm" data-control="select2" data-placeholder="@lang('kazitds::kazitds.Select Product')">
              <option value="">@lang('kazitds::kazitds.Select Product')</option>
              @foreach ($productVariants as $variant)
                <option value="{{ $variant->id }}" {{ request('product_variant') == $variant->id ? 'selected' : '' }}>
                  {{ $variant->product->name }} - {{ $variant->brand->name ?? '--' }} - {{ $variant->pack->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <input type="date" name="start_date" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.Start Date')" value="{{ request('start_date') }}">
          </div>

          <div class="col-md-3">
            <input type="date" name="end_date" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.End Date')" value="{{ request('end_date') }}">
          </div>

          <div class="col-md-2">
            <button type="submit" class="btn btn-sm btn-encodex-search rounded">
              <i class="fas fa-search"></i> @lang('kazitds::kazitds.Search')
            </button>

            <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-encodex-clear rounded">
              <i class="fas fa-eraser"></i> @lang('kazitds::kazitds.Reset')
            </a>
          </div>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover table-striped table-encodex mb-2">
          <thead class="bg-primary text-white">
            <tr>
              <th>{{ trans('kazitds::kazitds.#') }}</th>
              <th>@lang('kazitds::kazitds.Purchase Number')</th>
              <th>@lang('kazitds::kazitds.Date')</th>
              <th>@lang('kazitds::kazitds.Product')</th>
              <th>@lang('kazitds::kazitds.Quantity')</th>
              <th>@lang('kazitds::kazitds.Price')</th>
              <th>@lang('kazitds::kazitds.Total')</th>
              <th>@lang('kazitds::kazitds.Supplier')</th>
              <th>@lang('kazitds::kazitds.Action')</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($purchases as $purchase)
              <tr>
                <td>{{ toBanglaNumber($loop->iteration) }}</td>
                <td>{{ $purchase->purchase_number ?? __('kazitds::kazitds.N/A') }}</td>
                <td>{{ formatDate($purchase->purchase_date) }}</td>
                <td>
                  {{ $purchase->productVariant->product->name }} -
                  {{ $purchase->productVariant->brand->name ?? '--' }} -
                  {{ $purchase->productVariant->pack->name }}
                </td>
                <td class="text-end">{{ toBanglaNumber($purchase->quantity) }}</td>
                <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($purchase->price_per_unit, 2) }}</td>
                <td class="text-end">@lang("kazitds::kazitds.TK.") {{ toBanglaNumber($purchase->total_price, 2) }}</td>
                <td>{{ $purchase->supplier->name ?? __('kazitds::kazitds.N/A') }}</td>
                <td class="d-flex justify-content-center">
                  <a title="@lang("kazitds::kazitds.View")" class="btn btn-sm btn-encodex-show "
                      href="{{ route('purchases.show', $purchase->id) }}">
                      <i class="fas fa-eye "></i>
                  </a>

                  <a title="@lang("kazitds::kazitds.Edit")" class="btn btn-sm btn-encodex-edit "
                      href="{{ route('purchases.edit', $purchase->id) }}">
                      <i class="fas fa-edit "></i>
                  </a>

                  <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                        <button title="@lang("kazitds::kazitds.Delete")" onclick="return confirm('{{ __('kazitds::kazitds.Are you sure you want to delete this?') }}')"
                            type="submit" class="btn btn-sm btn-encodex-delete p-0 px-1 me-0">
                            <i class="fas fa-trash "></i>
                        </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center">@lang('kazitds::kazitds.No purchases found')</td>
              </tr>
            @endforelse
          </tbody>
        </table>
            @if(method_exists($purchases, 'links'))
            {{ $purchases->links('pagination::bootstrap-5') }}
            @endif
      </div>
    </div>
  </div>
@endsection

