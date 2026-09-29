@extends('me::master')

@section('title', trans('kazitds::kazitds.Packs'))

@push('buttons')
  <button type="button" class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#createModal">
      <i class="fas fa-plus"></i> @lang('kazitds::kazitds.Add Pack')
  </button>
@endpush

@section('content')
  <div class="card border-left-primary shadow h-100 w-100 py-2">
    <div class="card-body">
      <form method="GET" action="{{ route('packs.index') }}" class="mb-3">
        <div class="row">
          <div class="col-md">
            <input type="text" name="name" class="form-control form-control-sm" placeholder="@lang('kazitds::kazitds.Enter Pack Name')" value="{{ request('name') }}">
          </div>

          <div class="col-md">
            <button type="submit" class="btn btn-sm btn-encodex-search rounded">
              <i class="fas fa-search"></i> @lang('kazitds::kazitds.Search')
            </button>

            <a href="{{ route('packs.index') }}" class="btn btn-sm btn-encodex-clear rounded">
              <i class="fas fa-eraser"></i> @lang('kazitds::kazitds.Reset')
            </a>
          </div>
        </div>
      </form>

      <table class="table table-sm table-bordered table-hover table-striped table-encodex">
        <thead class="bg-primary text-white">
          <tr>
            <th>{{ trans('kazitds::kazitds.#') }}</th>
            <th>@lang('kazitds::kazitds.Pack Name')</th>
            <th>@lang('kazitds::kazitds.Action')</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($packs as $pack)
            <tr>
              <td>{{ toBanglaNumber($loop->iteration) }}</td>
              <td>{{ $pack->name }}</td>
              <td class="d-flex justify-content-center">
                <button type="button" title="@lang("kazitds::kazitds.Edit")" class="btn btn-sm btn-encodex-edit "
                    data-bs-toggle="modal" data-bs-target="#editModal{{ $pack->id }}">
                    <i class="fas fa-edit "></i>
                </button>

                <form action="{{ route('packs.destroy', $pack->id) }}" method="POST">
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
      {{ $packs->links('pagination::bootstrap-5') }}
    </div>
  </div>

  {{-- Create modal --}}
  <div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog glass-card">
      <form action="{{ route('packs.store') }}" method="POST" class="modal-content">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">@lang('kazitds::kazitds.Add Pack')</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @include('kazitds::packs._form', ['pack' => null, 'modalId' => 'createModal'])
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-encodex-cancel" data-bs-dismiss="modal">@lang('kazitds::kazitds.Cancel')</button>
          <button type="submit" class="btn btn-sm btn-encodex-save">@lang('kazitds::kazitds.Create Pack')</button>
        </div>
      </form>
    </div>
  </div>

  {{-- One edit modal per pack (plus the one asked for by ?edit= when it's on another page) --}}
  @foreach (collect($packs->items())->when($editItem, fn ($items) => $items->push($editItem))->unique('id') as $pack)
    <div class="modal fade" id="editModal{{ $pack->id }}" tabindex="-1">
      <div class="modal-dialog glass-card">
        <form action="{{ route('packs.update', $pack->id) }}" method="POST" class="modal-content">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">@lang('kazitds::kazitds.Edit Pack')</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            @include('kazitds::packs._form', ['modalId' => 'editModal' . $pack->id])
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-encodex-cancel" data-bs-dismiss="modal">@lang('kazitds::kazitds.Cancel')</button>
            <button type="submit" class="btn btn-sm btn-encodex-save">@lang('kazitds::kazitds.Update Pack')</button>
          </div>
        </form>
      </div>
    </div>
  @endforeach

  @include('kazitds::partials.modal-scripts')
@endsection
