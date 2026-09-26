@extends('me::master')

@section('title', trans('kazitds::kazitds.Users'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('users.create'),
      'text' => __('kazitds::kazitds.Add User'),
      'class' => 'btn-encodex-create'
  ])
  @endcomponent
@endpush

@section('content')
<div class="container-fluids">
    <div class="card shadow mb-4 w-100">

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover table-striped table-encodex table-sm">
                    <thead class="text-center">
                        <tr>
                            <th>#</th>
                            <th>@lang('kazitds::kazitds.Avatar')</th>
                            <th>@lang('kazitds::kazitds.Name')</th>
                            <th>@lang('kazitds::kazitds.Email')</th>
                            <th>@lang('kazitds::kazitds.Roles')</th>
                            <th>@lang('kazitds::kazitds.Status')</th>
                            <th>@lang('kazitds::kazitds.Created At')</th>
                            <th>@lang('kazitds::kazitds.Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ toBanglaNumber($loop->iteration) }}</td>
                                <td class="text-center">
                                    @if($user->profile_image)
                                        <img src="{{ route('profile_img.show', ($user->profile_image)) }}"
                                             alt="{{ $user->name }}" class="rounded-circle"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 40px; margin: 0 auto;">
                                            <i class="fas fa-user text-secondary"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-info">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">@lang('kazitds::kazitds.Active')</span>
                                    @else
                                        <span class="badge bg-danger">@lang('kazitds::kazitds.Inactive')</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at ? $user->created_at->format('d M Y') : __('kazitds::kazitds.N/A') }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm me-1 btn-encodex-show" title="@lang("kazitds::kazitds.View")">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm me-1 btn-encodex-edit {{ $user->id === auth()->id() ? 'disabled-link' : '' }}" title="@lang("kazitds::kazitds.Edit")" >
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('users.toggle-active', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm me-1 {{ $user->is_active ? 'btn-encodex-deactive' : 'btn-encodex-active' }}"
                                                title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}"
                                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                                <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-encodex-delete" title="@lang("kazitds::kazitds.Delete")"
                                                onclick="return confirm('{{ __('kazitds::kazitds.Are you sure you want to delete this?') }}')"
                                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">@lang('kazitds::kazitds.No users found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
    <style>
        .disabled-link {
            pointer-events: none; /* click নিষ্ক্রিয় করবে */
            color: gray;          /* visual disable effect */
            text-decoration: none;
        }
    </style>
@endpush
