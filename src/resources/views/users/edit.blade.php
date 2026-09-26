@extends('me::master')

@section('title', trans('kazitds::kazitds.Edit User'))

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
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="profile_image" class="font-weight-bold text-primary">
                                <i class="fas fa-image me-1"></i> @lang('kazitds::kazitds.Profile Image')
                            </label>
                            @if($user->profile_image)
                                <div class="mb-2">
                                    <img src="{{ route('profile_img.show', ($user->profile_image)) }}"
                                         alt="{{ $user->name }}" class="img-thumbnail" style="max-width: 150px;">
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" class="custom-file-input border border-primary @error('profile_image') is-invalid @enderror"
                                       id="profile_image" name="profile_image" accept="image/*">
                                <small class="form-text text-muted">@lang('kazitds::kazitds.Leave empty to keep current image. Recommended size: 300x300px. Max: 2MB')</small>
                            </div>
                            @error('profile_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="name" class="font-weight-bold text-primary">
                                <i class="fas fa-user me-1"></i> @lang('kazitds::kazitds.Name') <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name"
                                   value="{{ old('name', $user->name) }}" required autocomplete="off">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="font-weight-bold text-primary">
                                <i class="fas fa-envelope me-1"></i> @lang('kazitds::kazitds.Email') <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email"
                                   value="{{ old('email', $user->email) }}" required autocomplete="new-email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone" class="font-weight-bold text-primary">
                                <i class="fas fa-phone me-1"></i> @lang('kazitds::kazitds.Phone Number')
                            </label>
                            <input type="text" class="form-control form-control-sm @error('phone') is-invalid @enderror" id="phone" name="phone"
                                   value="{{ old('phone', $user->phone) }}" autocomplete="off">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="font-weight-bold text-primary">
                                <i class="fas fa-lock me-1"></i> @lang('kazitds::kazitds.Password')
                                <small class="text-muted">(@lang('kazitds::kazitds.Leave blank to keep current password'))</small>
                            </label>
                            <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="font-weight-bold text-primary">
                                <i class="fas fa-lock me-1"></i> @lang('kazitds::kazitds.Confirm Password')
                            </label>
                            <input type="password" class="form-control form-control-sm" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">
                                <i class="fas fa-user-shield me-1"></i> @lang('kazitds::kazitds.Role') <span class="text-danger">*</span>
                            </label>
                            <div class="border p-3 rounded">
                                @foreach($roles as $role)
                                    <div class="custom-control custom-radio mb-2">
                                        <input type="radio" class="custom-control-input"
                                            id="role_{{ $role->id }}"
                                            name="role"
                                            value="{{ $role->id }}"
                                            {{ in_array($role->id, old('role', $userRoles)) || old('role') == $role->id ? 'checked' : '' }}
                                            required
                                            @if($user->id == 1) disabled @endif
                                        >
                                        <label class="custom-control-label" for="role_{{ $role->id }}">
                                            {{ $role->name }} <small class="text-muted">- {{ $role->description }}</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @if($user->id == 1)
                                <small class="form-text text-danger">@lang('kazitds::kazitds.Role cannot be changed for the super admin.')</small>
                            @endif
                            @error('role')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3 mt-4">
                            <div class="custom-control custom-switch {{ $user->id === auth()->id() ? 'disabled' : '' }}">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                       {{ $user->id === auth()->id() ? 'disabled checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="is_active">
                                    <i class="fas fa-toggle-on me-1"></i> @lang('kazitds::kazitds.Active User')
                                </label>
                                @if($user->id === auth()->id())
                                    <small class="form-text text-danger">@lang('kazitds::kazitds.You cannot deactivate your own account.')</small>
                                    <input type="hidden" name="is_active" value="1">
                                @else
                                    <small class="form-text text-muted">@lang('kazitds::kazitds.Inactive users cannot log in to the system.')</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-encodex float-right">
                        <i class="fas fa-save me-1"></i> @lang('kazitds::kazitds.Update User')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Clear autofilled values on page load
    window.addEventListener('load', function() {
        setTimeout(function() {
            // Store the current value of email
            var emailValue = document.getElementById('email').value;

            // Clear the field
            document.getElementById('email').value = '';

            // Restore the value after a brief delay
            setTimeout(function() {
                document.getElementById('email').value = emailValue;
                document.getElementById('password').value = '';
                document.getElementById('password_confirmation').value = '';
            }, 50);
        }, 100);
    });
</script>
@endpush

@endsection
