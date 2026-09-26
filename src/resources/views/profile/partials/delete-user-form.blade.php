<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('kazitds::kazitds.Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('kazitds::kazitds.Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button
        type="button"
        class="btn btn-danger mt-3"
        data-toggle="modal"
        data-target="#confirm-user-deletion-modal"
    >
        {{ __('kazitds::kazitds.Delete Account') }}
    </button>

    <!-- Modal -->
    <div class="modal fade" id="confirm-user-deletion-modal" tabindex="-1" role="dialog" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content p-4">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmUserDeletionLabel">{{ __('kazitds::kazitds.Are you sure you want to delete your account?') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('kazitds::kazitds.Close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-sm text-gray-600">
                        {{ __('kazitds::kazitds.Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                    <div class="form-group mt-3">
                        <label for="delete_user_password" class="sr-only">{{ __('kazitds::kazitds.Password') }}</label>
                        <input
                            id="delete_user_password"
                            name="password"
                            type="password"
                            class="form-control"
                            placeholder="{{ __('kazitds::kazitds.Password') }}"
                        >
                        @if($errors->userDeletion->has('password'))
                            <div class="text-danger mt-2">{{ $errors->userDeletion->first('password') }}</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-encodex-cancel" data-dismiss="modal">
                        {{ __('kazitds::kazitds.Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-danger">
                        {{ __('kazitds::kazitds.Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
