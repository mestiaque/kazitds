@extends('me::master')

@section('title', trans('kazitds::kazitds.Clear Data'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        @lang('kazitds::kazitds.Clear All Data')
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-skull-crossbones mr-2"></i>
                            @lang('kazitds::kazitds.DANGER ZONE')
                        </h5>
                        <p>@lang('kazitds::kazitds.This action will permanently delete ALL data from the system including:')</p>
                        <ul class="mb-0">
                            <li>@lang('kazitds::kazitds.All Products and Product Variants')</li>
                            <li>@lang('kazitds::kazitds.All Sales Records and Sale Items')</li>
                            <li>@lang('kazitds::kazitds.All Purchase Records')</li>
                            <li>@lang('kazitds::kazitds.All Brands and Packs')</li>
                            <li>@lang('kazitds::kazitds.All Suppliers and Customers')</li>
                            <li>@lang('kazitds::kazitds.All Stock Records')</li>
                        </ul>
                        <hr>
                        <p class="mb-0">
                            <strong>@lang('kazitds::kazitds.Users and Roles will be preserved.')</strong><br>
                            <strong class="text-danger">@lang('kazitds::kazitds.This action cannot be undone!')</strong>
                        </p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle mr-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('data.clear') }}" onsubmit="return confirmClear()">
                        @csrf

                        <div class="form-group">
                            <label for="confirm_text" class="font-weight-bold">
                                @lang('kazitds::kazitds.Type') <code>CLEAR ALL DATA</code> @lang('kazitds::kazitds.to confirm'):
                            </label>
                            <input type="text"
                                   name="confirm_text"
                                   id="confirm_text"
                                   class="form-control @error('confirm_text') is-invalid @enderror"
                                   placeholder="CLEAR ALL DATA"
                                   autocomplete="off">
                            @error('confirm_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit"
                                    class="btn btn-danger shadow btn-lg"
                                    id="clearButton"
                                    disabled>
                                <i class="fas fa-trash-alt mr-2"></i>
                                @lang('kazitds::kazitds.Clear All Data')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('#confirm_text').on('input', function() {
        const confirmText = $(this).val();
        const clearButton = $('#clearButton');

        if (confirmText === 'CLEAR ALL DATA') {
            clearButton.prop('disabled', false);
        } else {
            clearButton.prop('disabled', true);
        }
    });
});

function confirmClear() {
    return confirm('Are you absolutely sure you want to clear all data? This action cannot be undone!');
}
</script>
@endpush

@push('css')
<style>
    .alert-danger {
        border-left: 5px solid #dc3545;
    }

    code {
        background: #f8f9fa;
        padding: 2px 4px;
        border-radius: 3px;
        color: #dc3545;
        font-weight: bold;
    }
</style>
@endpush
