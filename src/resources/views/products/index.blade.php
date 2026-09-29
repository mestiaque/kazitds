@extends('me::master')

@section('title', trans('kazitds::kazitds.Products'))

@push('buttons')
  <button type="button" class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#createModal">
      <i class="fas fa-plus"></i> @lang('kazitds::kazitds.Add Product')
  </button>
@endpush

@section('content')
  <div class="card border-left-primary shadow h-100 w-100 py-2">
    <div class="card-body">
      <form method="GET" action="{{ route('products.index') }}" class="mb-3">
        <div class="row">
          <div class="col-md">
            <input type="text" name="name" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.Enter Product Name')" value="{{ request('name') }}">
          </div>

          <div class="col-md">
            <button type="submit" class="btn btn-sm btn-encodex-search rounded">
              <i class="fas fa-search"></i> @lang('kazitds::kazitds.Search')
            </button>

            <a href="{{ route('products.index') }}" class="btn btn-sm btn-encodex-clear rounded">
              <i class="fas fa-eraser"></i> @lang('kazitds::kazitds.Reset')
            </a>
          </div>
        </div>
      </form>

      <table class="table table-sm table-bordered table-hover table-striped table-encodex">
        <thead class="">
          <tr>
            <th>{{ trans('kazitds::kazitds.#') }}</th>
            <th>@lang('kazitds::kazitds.Product')</th>
            {{-- <th>@lang('kazitds::kazitds.Image')</th> --}}
            <th class="text-center">@lang('kazitds::kazitds.Action')</th>
          </tr>
        </thead>
        <tbody id="massSelectArea">
          @foreach ($products as $product)
            <tr>
              <td>{{ toBanglaNumber($loop->iteration) }}</td>
              <td>{{ $product->name }}</td>
              {{-- <td>
                @if ($product->image)
                  <img src="{{ route('attachments.show', ($product->image)) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 50px; height: 50px;">
                @else
                  <span>@lang('kazitds::kazitds.No Image')</span>
                @endif
              </td> --}}
              <td class="d-flex justify-content-center">
                <button type="button" title="@lang("kazitds::kazitds.Edit")" class="btn btn-sm btn-encodex-edit "
                    data-bs-toggle="modal" data-bs-target="#editModal{{ $product->id }}">
                    <i class="fas fa-edit "></i>
                </button>

                <form action="{{ route('products.destroy', $product->id) }}" method="POST">
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
      {{ $products->links('pagination::bootstrap-5') }}
    </div>
  </div>

  {{-- Create modal --}}
  <div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog glass-card">
      <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">@lang('kazitds::kazitds.Add Product')</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @include('kazitds::products._form', ['product' => null, 'modalId' => 'createModal'])
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-encodex-cancel" data-bs-dismiss="modal">@lang('kazitds::kazitds.Cancel')</button>
          <button type="submit" class="btn btn-sm btn-encodex-save">@lang('kazitds::kazitds.Create Product')</button>
        </div>
      </form>
    </div>
  </div>

  {{-- One edit modal per product (plus the one asked for by ?edit= when it's on another page) --}}
  @foreach (collect($products->items())->when($editItem, fn ($items) => $items->push($editItem))->unique('id') as $product)
    <div class="modal fade" id="editModal{{ $product->id }}" tabindex="-1">
      <div class="modal-dialog glass-card">
        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">@lang('kazitds::kazitds.Edit Product')</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            @include('kazitds::products._form', ['modalId' => 'editModal' . $product->id])
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-encodex-cancel" data-bs-dismiss="modal">@lang('kazitds::kazitds.Cancel')</button>
            <button type="submit" class="btn btn-sm btn-encodex-save">@lang('kazitds::kazitds.Update Product')</button>
          </div>
        </form>
      </div>
    </div>
  @endforeach

  @include('kazitds::partials.modal-scripts')
@endsection
