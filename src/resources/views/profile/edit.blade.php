@extends('me::master')

@section('title', __('kazitds::kazitds.Profile'))

@section('content')
<div class="container-fluids">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-encodex text-white p-2">
                    <h6 class="mb-0">@lang('kazitds::kazitds.Update Profile Information')</h6>
                </div>
                <div class="card-body">
                    @include('kazitds::profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-encodex-light text-white p-2">
                    <h6 class="mb-0">@lang('kazitds::kazitds.Update Password')</h6>
                </div>
                <div class="card-body">
                    @include('kazitds::profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
