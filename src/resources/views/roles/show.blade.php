@extends('me::master')

@section('title', trans('kazitds::kazitds.Role Details'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('roles.index'),
      'text' => __('kazitds::kazitds.All Roles'),
      'class' => 'btn-encodex-list'
  ])
  @endcomponent
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Role Details Card -->
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-2 bg-encodex">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-user-tag me-1"></i> @lang('kazitds::kazitds.Role Information')
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm table-hover table-striped table-encodex">
                        <tbody>
                            <tr>
                                <th width="30%">@lang('kazitds::kazitds.Name')</th>
                                <td>{{ $role->name }}</td>
                            </tr>
                            <tr>
                                <th>@lang('kazitds::kazitds.Slug')</th>
                                <td><code>{{ $role->slug }}</code></td>
                            </tr>
                            <tr>
                                <th>@lang('kazitds::kazitds.Description')</th>
                                <td>{{ $role->description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>@lang('kazitds::kazitds.Created At')</th>
                                <td>{{ optional($role->created_at)->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>@lang('kazitds::kazitds.Updated At')</th>
                                <td>{{ optional($role->updated_at)->format('M d, Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Permissions Card -->
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-2 bg-encodex">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-key me-1"></i> @lang('kazitds::kazitds.Permissions')
                    </h6>
                </div>
            <div class="card-body">
            @php
                $permissions = $role->rolePermission ? $role->rolePermission->permissions : [];

                $groupedPermissions = collect($permissions)->groupBy(function ($item) {
                    return explode('.', $item)[0];
                });
            @endphp

            @if(empty($permissions))
                <div class="alert alert-info mb-0">
                    @lang('kazitds::kazitds.No permissions assigned to this role.')
                </div>
            @else
                <div class="row">
                    @foreach($groupedPermissions as $module => $modulePermissions)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-light py-1">
                                    <strong>{{ ucfirst($module) }}</strong>
                                </div>
                                <div class="card-body py-2">
                                    <div class="row">
                                        @foreach($modulePermissions as $permission)
                                            @php
                                                $action = explode('.', $permission)[1] ?? '';
                                            @endphp
                                            <div class="col-6 mb-2 ">
                                                <span class="badge badge-pill bg-encodex-secondary px-3 py-2 text-start">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    {{ ucfirst($action) }}
                                                </span>
                                            </div>
                                        @endforeach
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


    <!-- Users Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-2 bg-encodex-light">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-users me-1"></i> @lang('kazitds::kazitds.Users with this Role')
            </h6>
        </div>
        <div class="card-body">
            @if($role->users->isEmpty())
                <div class="alert alert-info">
                    @lang('kazitds::kazitds.No users currently have this role.')
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover table-striped table-encodex" width="100%" cellspacing="0">
                        <thead class="text-center">
                            <tr>
                                <th>@lang('kazitds::kazitds.Name')</th>
                                <th>@lang('kazitds::kazitds.Email')</th>
                                <th>@lang('kazitds::kazitds.Joined')</th>
                                <th>@lang('kazitds::kazitds.Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role->users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($user->profile_image)
                                                <img src="{{ route('profile_img.show', ($user->profile_image)) }}"
                                                    alt="{{ $user->name }}" class="rounded-circle"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px; margin: 0 auto;">
                                                    <i class="fas fa-user text-secondary"></i>
                                                </div>
                                            @endif &nbsp;
                                            {{ $user->name }}
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ optional($user->created_at)->format('M d, Y') ?? '--' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-encodex-show">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .badge-pill.badge-primary {
        font-size: 0.85rem;
    }
    table td{
        vertical-align: middle !important;
    }
</style>
@endpush
