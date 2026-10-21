@extends('me::master')

@section('title', trans('kazitds::kazitds.Purchases'))

@push('buttons')
  <button type="button" class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#createModal">
      <i class="fas fa-plus"></i> @lang('kazitds::kazitds.Add Purchase')
  </button>
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
            <input type="date" name="start_date" class="form-control form-control-sm" title="@lang('kazitds::kazitds.Start Date')" value="{{ request('start_date') }}">
          </div>

          <div class="col-md-3">
            <input type="date" name="end_date" class="form-control form-control-sm" title="@lang('kazitds::kazitds.End Date')" value="{{ request('end_date') }}">
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
                <td>{{ toBanglaNumber($purchases->firstItem() + $loop->index) }}</td>
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
                  <button type="button" title="@lang("kazitds::kazitds.View")" class="btn btn-sm btn-encodex-show"
                      data-bs-toggle="modal" data-bs-target="#showModal{{ $purchase->id }}">
                      <i class="fas fa-eye"></i>
                  </button>

                  <button type="button" title="@lang("kazitds::kazitds.Edit")" class="btn btn-sm btn-encodex-edit"
                      data-bs-toggle="modal" data-bs-target="#editModal{{ $purchase->id }}">
                      <i class="fas fa-edit"></i>
                  </button>

                  <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                        <button title="@lang("kazitds::kazitds.Delete")" onclick="return confirm('{{ __('kazitds::kazitds.Are you sure you want to delete this?') }}')"
                            type="submit" class="btn btn-sm btn-encodex-delete p-0 px-1 me-0">
                            <i class="fas fa-trash"></i>
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
        {{ $purchases->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>

  {{-- Add modal --}}
  <div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog glass-card modal-lg">
      <form action="{{ route('purchases.store') }}" method="POST" class="modal-content">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">@lang('kazitds::kazitds.Add Purchase')</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @include('kazitds::purchases._form', ['purchase' => null, 'modalId' => 'createModal', 'defaultVariantId' => request('product_variant_id')])
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-encodex-cancel" data-bs-dismiss="modal">@lang('kazitds::kazitds.Cancel')</button>
          <button type="submit" class="btn btn-sm btn-savex">@lang('kazitds::kazitds.Create Purchase')</button>
        </div>
      </form>
    </div>
  </div>

  {{-- One view modal and one edit modal per purchase (plus the one asked for by ?show= / ?edit= when it's on another page) --}}
  @foreach (collect($purchases->items())->when($focusItem, fn ($items) => $items->push($focusItem))->unique('id') as $purchase)
    <div class="modal fade" id="showModal{{ $purchase->id }}" tabindex="-1">
      <div class="modal-dialog glass-card modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">@lang('kazitds::kazitds.Purchase Details')</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            @include('kazitds::purchases._details')
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-encodex-cancel" data-bs-dismiss="modal">@lang('kazitds::kazitds.Close')</button>
            <button type="button" class="btn btn-sm btn-encodex-edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $purchase->id }}">
              <i class="fas fa-edit"></i> @lang('kazitds::kazitds.Edit')
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editModal{{ $purchase->id }}" tabindex="-1">
      <div class="modal-dialog glass-card modal-lg">
        <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" class="modal-content">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">@lang('kazitds::kazitds.Edit Purchase') <small class="text-muted">#{{ $purchase->purchase_number }}</small></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            @include('kazitds::purchases._form', ['modalId' => 'editModal' . $purchase->id])
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-encodex-cancel" data-bs-dismiss="modal">@lang('kazitds::kazitds.Cancel')</button>
            <button type="submit" class="btn btn-sm btn-savex">@lang('kazitds::kazitds.Update Purchase')</button>
          </div>
        </form>
      </div>
    </div>
  @endforeach

  @include('kazitds::partials.modal-scripts')
@endsection

@push('js')
<script>
    // Total = quantity × unit price, worked out inside each purchase modal on its own
    $(function () {
        const updateTotal = function (form) {
            const qty = parseFloat($(form).find('.js-purchase-qty').val()) || 0;
            const price = parseFloat($(form).find('.js-purchase-price').val()) || 0;
            $(form).find('.js-purchase-total').val((qty * price).toFixed(2));
        };
        $(document).on('input', '.purchase-form .js-purchase-qty, .purchase-form .js-purchase-price', function () {
            updateTotal($(this).closest('.purchase-form'));
        });
        $('.purchase-form').each(function () { updateTotal(this); });
    });
</script>
@endpush
