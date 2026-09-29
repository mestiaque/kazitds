@extends('me::master')

@section('title', trans('kazitds::kazitds.Product Variants'))

@push('buttons')
  <button type="button" class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#createModal">
      <i class="fas fa-plus"></i> @lang('kazitds::kazitds.Add Product Variant')
  </button>
@endpush

@section('content')
  <div class="card border-left-primary shadow h-100 w-100 py-2">
    <div class="card-body">
      <form method="GET" action="{{ route('product-variants.index') }}" class="mb-3">
        <div class="row">
          <div class="col-md-3">
            <select name="product" class="form-select form-select-sm" data-control="select2" data-placeholder="@lang('kazitds::kazitds.Select Product')">
              <option value="">@lang('kazitds::kazitds.Select Product')</option>
              @foreach ($products as $product)
                <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                  {{ $product->name }} {{ $product->bn_name ? '- '.$product->bn_name : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <select name="brand" class="form-control form-control-sm" data-control="select2" data-placeholder="@lang('kazitds::kazitds.Select Brand')">
              <option value="">@lang('kazitds::kazitds.Select Brand')</option>
              @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                  {{ $brand->name }} {{ $brand->bn_name ? '- '.$brand->bn_name : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <select name="pack" class="form-control form-control-sm" data-control="select2" data-placeholder="@lang('kazitds::kazitds.Select Pack')">
              <option value="">@lang('kazitds::kazitds.Select Pack')</option>
              @foreach ($packs as $pack)
                <option value="{{ $pack->id }}" {{ request('pack') == $pack->id ? 'selected' : '' }}>
                  {{ $pack->name }} {{ $pack->bn_name ? '- '.$pack->bn_name : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <button type="submit" class="btn btn-sm btn-encodex-search rounded">
              <i class="fas fa-search"></i> @lang('kazitds::kazitds.Search')
            </button>

            <a href="{{ route('product-variants.index') }}" class="btn btn-sm btn-encodex-clear rounded">
              <i class="fas fa-eraser"></i> @lang('kazitds::kazitds.Reset')
            </a>
          </div>
        </div>
      </form>

      <table class="table table-sm table-bordered table-hover table-striped table-encodex">
        <thead class="bg-primary text-white">
          <tr>
            <th>{{ trans('kazitds::kazitds.#') }}</th>
            <th>@lang('kazitds::kazitds.Product')</th>
            <th>@lang('kazitds::kazitds.Brand')</th>
            <th>@lang('kazitds::kazitds.Pack')</th>
            <th>@lang('kazitds::kazitds.Status')</th>
            <th>@lang('kazitds::kazitds.Action')</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($productVariants as $variant)
            <tr>
              <td>{{ toBanglaNumber($loop->iteration) }}</td>
              <td>{{ $variant->product->name }}</td>
              <td>{{ $variant->brand->name ?? '--' }}</td>
              <td>{{ $variant->pack->name }}</td>
              <td>
                    <span class="badge {{ $variant->is_active ? 'bg-success' : 'bg-danger' }}">
                    {{ $variant->is_active ? __('kazitds::kazitds.Active') : __('kazitds::kazitds.Inactive') }}
                    </span>
              </td>
              <td class="d-flex justify-content-center">
                <button type="button" title="@lang("kazitds::kazitds.Edit")" class="btn btn-sm btn-encodex-edit "
                    data-bs-toggle="modal" data-bs-target="#editModal{{ $variant->id }}">
                    <i class="fas fa-edit "></i>
                </button>

                <form action="{{ route('product-variants.destroy', $variant->id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                     <button title="@lang("kazitds::kazitds.Delete")" onclick="return confirm('{{ __('kazitds::kazitds.Are you sure you want to delete this?') }}')"
                      type="submit" class="btn btn-sm btn-encodex-delete ">
                      <i class="fas fa-trash "></i>
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      {{ $productVariants->links('pagination::bootstrap-5') }}
    </div>
  </div>

  {{-- Create modal --}}
  <div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog glass-card modal-lg">
      <form action="{{ route('product-variants.store') }}" method="POST" class="modal-content">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">@lang('kazitds::kazitds.Add Product Variant')</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @include('kazitds::product_variants._form', ['productVariant' => null, 'modalId' => 'createModal'])
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-encodex-cancel" data-bs-dismiss="modal">@lang('kazitds::kazitds.Cancel')</button>
          <button type="submit" class="btn btn-sm btn-encodex-save">@lang('kazitds::kazitds.Create Product Variant')</button>
        </div>
      </form>
    </div>
  </div>

  {{-- One edit modal per variant (plus the one asked for by ?edit= when it's on another page) --}}
  @foreach (collect($productVariants->items())->when($editItem, fn ($items) => $items->push($editItem))->unique('id') as $variant)
    <div class="modal fade" id="editModal{{ $variant->id }}" tabindex="-1">
      <div class="modal-dialog glass-card modal-lg">
        <form action="{{ route('product-variants.update', $variant->id) }}" method="POST" class="modal-content">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">@lang('kazitds::kazitds.Edit Product Variant')</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            @include('kazitds::product_variants._form', ['productVariant' => $variant, 'modalId' => 'editModal' . $variant->id])
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-encodex-cancel" data-bs-dismiss="modal">@lang('kazitds::kazitds.Cancel')</button>
            <button type="submit" class="btn btn-sm btn-encodex-save">@lang('kazitds::kazitds.Update Product Variant')</button>
          </div>
        </form>
      </div>
    </div>
  @endforeach

  @include('kazitds::partials.modal-scripts')
@endsection
