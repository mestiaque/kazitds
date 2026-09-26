@extends('me::master')

@section('title', trans('kazitds::kazitds.Roles'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('roles.create'),
      'text' => __('kazitds::kazitds.Add Role'),
      'class' => 'btn-encodex-list'
  ])
  @endcomponent
@endpush

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 w-100">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped table-encodex table-sm">
                    <thead class="text-center">
                        <tr>
                            <th>#</th>
                            <th>@lang('kazitds::kazitds.Role Name')</th>
                            <th>@lang('kazitds::kazitds.Slug')</th>
                            <th>@lang('kazitds::kazitds.Description')</th>
                            <th>@lang('kazitds::kazitds.Users')</th>
                            <th>@lang('kazitds::kazitds.Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>{{ toBanglaNumber($loop->iteration) }}</td>
                                <td>{{ $role->name }}</td>
                                <td><code>{{ $role->slug }}</code></td>
                                <td>{{ $role->description ?? __('kazitds::kazitds.N/A') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $role->users_count }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="{{ route('roles.show', $role->id) }}" class="btn btn-sm btn-encodex-show me-1" title="@lang("kazitds::kazitds.View")">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!in_array($role->slug, ['admin', 'super_admin']))
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-encodex-edit me-1" title="@lang("kazitds::kazitds.Edit")">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-encodex-delete" title="@lang("kazitds::kazitds.Delete")"
                                                onclick="return confirm('{{ __('kazitds::kazitds.Are you sure you want to delete this?') }}')"
                                                {{ in_array($role->slug, ['admin', 'super_admin']) ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">@lang('kazitds::kazitds.No roles found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $roles->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
