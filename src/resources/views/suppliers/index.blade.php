@extends('me::master')

@section('title', trans('kazitds::kazitds.Suppliers'))

@push('buttons')
  <button type="button" class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#createModal">
      <i class="fas fa-plus"></i> @lang('kazitds::kazitds.Add Supplier')
  </button>
@endpush

@section('content')
  <div class="card border-left-primary shadow h-100 w-100 py-2">
    <div class="card-body">
        <form method="GET" action="{{ route('suppliers.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md">
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.Enter Supplier Name')" value="{{ request('name') }}">
                </div>
                <div class="col-md">
                    <input type="text" name="phone" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.Enter Supplier Phone')" value="{{ request('phone') }}">
                </div>

                <div class="col-md">
                    <button type="submit" class="btn btn-sm btn-encodex-search rounded">
                        <i class="fas fa-search"></i> @lang('kazitds::kazitds.Search')
                    </button>

                    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-encodex-clear rounded">
                        <i class="fas fa-eraser"></i> @lang('kazitds::kazitds.Reset')
                    </a>
                </div>
            </div>
        </form>
      <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover table-striped table-encodex">
          <thead class="bg-primary text-white">
            <tr>
              <th>{{ trans('kazitds::kazitds.#') }}</th>
              <th>@lang('kazitds::kazitds.Name')</th>
              <th>@lang('kazitds::kazitds.Company')</th>
              <th>@lang('kazitds::kazitds.Phone')</th>
              <th>@lang('kazitds::kazitds.Email')</th>
              <th>@lang('kazitds::kazitds.Address')</th>
              <th>@lang('kazitds::kazitds.Action')</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($suppliers as $supplier)
              <tr>
                <td>{{ toBanglaNumber($loop->iteration) }}</td>
                <td>{{ $supplier->name }}</td>
                <td>{{ $supplier->company_name ?? __('kazitds::kazitds.N/A') }}</td>
                <td>{{ $supplier->phone ?? __('kazitds::kazitds.N/A') }}</td>
                <td>{{ $supplier->email ?? __('kazitds::kazitds.N/A') }}</td>
                <td>{{ $supplier->address ?? __('kazitds::kazitds.N/A') }}</td>
                <td class="d-flex justify-content-center">
                  <button type="button" title="@lang("kazitds::kazitds.Edit")" class="btn btn-sm btn-encodex-edit "
                      data-bs-toggle="modal" data-bs-target="#editModal{{ $supplier->id }}">
                      <i class="fas fa-edit "></i>
                  </button>

                  <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST">
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
        {{ $suppliers->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>

  {{-- Create modal --}}
  <div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog glass-card modal-lg">
      <form action="{{ route('suppliers.store') }}" method="POST" class="modal-content">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">@lang('kazitds::kazitds.Add Supplier')</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @include('kazitds::suppliers._form', ['supplier' => null, 'modalId' => 'createModal'])
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-encodex-cancel" data-bs-dismiss="modal">@lang('kazitds::kazitds.Cancel')</button>
          <button type="submit" class="btn btn-sm btn-encodex-save">@lang('kazitds::kazitds.Create Supplier')</button>
        </div>
      </form>
    </div>
  </div>

  {{-- One edit modal per supplier (plus the one asked for by ?edit= when it's on another page) --}}
  @foreach (collect($suppliers->items())->when($editItem, fn ($items) => $items->push($editItem))->unique('id') as $supplier)
    <div class="modal fade" id="editModal{{ $supplier->id }}" tabindex="-1">
      <div class="modal-dialog glass-card modal-lg">
        <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" class="modal-content">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">@lang('kazitds::kazitds.Edit Supplier')</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            @include('kazitds::suppliers._form', ['modalId' => 'editModal' . $supplier->id])
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-encodex-cancel" data-bs-dismiss="modal">@lang('kazitds::kazitds.Cancel')</button>
            <button type="submit" class="btn btn-sm btn-encodex-save">@lang('kazitds::kazitds.Update Supplier')</button>
          </div>
        </form>
      </div>
    </div>
  @endforeach

  @include('kazitds::partials.modal-scripts')
@endsection
