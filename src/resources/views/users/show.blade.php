@extends('me::master')

@section('title', trans('kazitds::kazitds.User Details'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('users.index'),
      'text' => __('kazitds::kazitds.All Users'),
      'class' => 'btn-encodex-list'
  ])
  @endcomponent
@endpush

@section('content')
<div class="container-fluids">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-2 bg-encodex">
                    <h6 class="m-0 font-weight-bold text-white">@lang('kazitds::kazitds.User Information')</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mb-3">
                            @if($user->profile_image)
                                <img src="{{ route('profile_img.show', $user->profile_image) }}" alt="{{ $user->name }}" class="img-fluid rounded-circle">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 98px; height: 98px; margin: 0 auto;">
                                    <i class="fas fa-user text-secondary" style="font-size: 60px"></i>
                                </div>
                            @endif
                        </div>
                        <h4 class="mb-0">{{ $user->name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>
                        <div class="mt-2">
                            @if($user->is_active)
                                <span class="badge bg-success badge-pill">@lang('kazitds::kazitds.Active')</span>
                            @else
                                <span class="badge bg-danger badge-pill">@lang('kazitds::kazitds.Inactive')</span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <dl>
                        <dt>@lang('kazitds::kazitds.Created At')</dt>
                        <dd>{{ optional($user->created_at)->format('d M Y H:i:s') ?? __('kazitds::kazitds.N/A') }}</dd>

                        <dt>@lang('kazitds::kazitds.Last Modified')</dt>
                        <dd>{{ optional($user->updated_at)->format('d M Y H:i:s') ?? __('kazitds::kazitds.N/A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-2 bg-encodex">
                    <h6 class="m-0 font-weight-bold text-white">@lang('kazitds::kazitds.Roles and Permissions')</h6>
                </div>
                <div class="card-body">
                    @if($user->roles->isEmpty())
                        <div class="alert alert-warning">
                            @lang('kazitds::kazitds.This user has no assigned roles.')
                        </div>
                    @else
                        <div class="row">
                            @foreach($user->roles as $role)
                                <div class="col-md-12 mb-4">
                                    <div class="card border-left-info shadow h-100">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                        @lang('kazitds::kazitds.Role')
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $role->name }}</div>
                                                    <small class="text-muted">{{ $role->description }}</small>

                                                    @php
                                                        $rolePermissions = ME\Kazitds\Models\RolePermission::where('role_id', $role->id)->first();
                                                        $permissions = $rolePermissions ? $rolePermissions->permissions : [];
                                                    @endphp

                                                    @if(!empty($permissions))
                                                        <hr>
                                                        <div class="mt-2">
                                                            <strong>@lang('kazitds::kazitds.Permissions'):</strong>
                                                            <div class="mt-2">
                                                                @foreach($permissions as $permission)
                                                                    @php
                                                                        $permParts = explode('.', $permission);
                                                                        $moduleName = ucfirst($permParts[0] ?? '');
                                                                        $actionName = ucfirst($permParts[1] ?? '');
                                                                        $displayName = $actionName . ' ' . $moduleName;
                                                                    @endphp
                                                                    <span class="badge badge-pill bg-secondary mb-1">
                                                                        {{ $displayName }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('css')
<style>
    .avatar-circle {
        width: 100px;
        height: 100px;
        background-color: #4e73df;
        text-align: center;
        border-radius: 50%;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .avatar-text {
        font-size: 42px;
        color: white;
        text-transform: uppercase;
    }
</style>
@endpush
