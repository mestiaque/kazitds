@extends('me::master')

@section('title', trans('kazitds::kazitds.Edit Role'))

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
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold text-primary">
                                <i class="fas fa-user-tag me-1"></i> @lang('kazitds::kazitds.Role Name') <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                            <small class="form-text text-muted">@lang('kazitds::kazitds.The name will be converted to a slug automatically.')</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="font-weight-bold text-primary">
                                <i class="fas fa-align-left me-1"></i> @lang('kazitds::kazitds.Description')
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-primary">
                                <i class="fas fa-key me-1"></i> @lang('kazitds::kazitds.Permissions')
                            </label>

                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text " style="padding:12px !important">
                                        <input type="checkbox" id="check-all-permissions">
                                    </div>
                                </div>
                                <div class="form-control">
                                    <label for="check-all-permissions">@lang('kazitds::kazitds.Select All Permissions')</label>
                                </div>
                            </div>

                            <div class="border p-3 rounded permission-list">
                                @if($permissions->isEmpty())
                                    <div class="text-center text-muted">
                                        @lang('kazitds::kazitds.No permissions defined in the system.')
                                    </div>
                                @else
                                    @foreach($permissions->groupBy(function($item) {
                                        return explode('.', $item->slug)[0];
                                    }) as $group => $items)
                                        <h6 class="font-weight-bold mt-3 mb-2 border-bottom pb-2">{{ ucfirst($group) }}</h6>
                                        <div class="row">
                                            @foreach($items as $permission)
                                                <div class="col-md-6">
                                                    <div class="custom-control custom-checkbox mb-2">
                                                        <input type="checkbox" class="custom-control-input permission-checkbox"
                                                              id="permission_{{ $permission->id }}" name="permissions[]"
                                                              value="{{ $permission->id }}"
                                                              {{ in_array($permission->id, $selectedPermissionIds) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @error('permissions')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-encodex float-right">
                        <i class="fas fa-save me-1"></i> @lang('kazitds::kazitds.Update Role')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Handle check all permissions
        $('#check-all-permissions').on('change', function() {
            $('.permission-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Update check all status when individual permissions change
        $('.permission-checkbox').on('change', function() {
            if ($('.permission-checkbox:checked').length === $('.permission-checkbox').length) {
                $('#check-all-permissions').prop('checked', true);
            } else {
                $('#check-all-permissions').prop('checked', false);
            }
        });

        // Set initial state for check-all box
        if ($('.permission-checkbox').length > 0 && $('.permission-checkbox:checked').length === $('.permission-checkbox').length) {
            $('#check-all-permissions').prop('checked', true);
        }
    });
</script>
@endpush

@push('css')
<style>
    .permission-list {
        max-height: 500px;
        overflow-y: auto;
    }
</style>
@endpush
