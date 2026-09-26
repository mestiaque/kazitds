<section>
    <form method="post" action="{{ route('password.update') }}" class="mt-1">
        @csrf
        @method('put')

        {{-- Current Password --}}
        <div class="form-group mb-3 position-relative">
            <label for="update_password_current_password" class="font-weight-bold text-primary">{{ __('kazitds::kazitds.Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
            <span class="toggle-password" data-target="update_password_current_password">
                <i class="fas fa-eye"></i>
            </span>
            @if($errors->updatePassword->has('current_password'))
                <div class="text-danger mt-2">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        {{-- New Password --}}
        <div class="form-group mb-3 position-relative">
            <label for="update_password_password" class="font-weight-bold text-primary">{{ __('kazitds::kazitds.New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password">
            <span class="toggle-password" data-target="update_password_password">
                <i class="fas fa-eye"></i>
            </span>
            @if($errors->updatePassword->has('password'))
                <div class="text-danger mt-2">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        {{-- Confirm Password --}}
        <div class="form-group mb-4 position-relative">
            <label for="update_password_password_confirmation" class="font-weight-bold text-primary">{{ __('kazitds::kazitds.Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
            <span class="toggle-password" data-target="update_password_password_confirmation">
                <i class="fas fa-eye"></i>
            </span>
            @if($errors->updatePassword->has('password_confirmation'))
                <div class="text-danger mt-2">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="form-group text-end">
            <button type="submit" class="btn btn-encodex">
                {{ __('kazitds::kazitds.Save Changes') }}
            </button>
        </div>
    </form>
</section>

@push('css')
<style>
    .toggle-password {
        position: absolute;
        top: 34px;
        right: 10px;
        cursor: pointer;
        color: #6c757d;
    }
</style>
@endpush
@push('js')
<script>
    document.querySelectorAll('.toggle-password').forEach(function (icon) {
        icon.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);

            if (input.type === 'password') {
                input.type = 'text';
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                input.type = 'password';
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
</script>
@endpush
