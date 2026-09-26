@extends('me::master')

@section('title', trans('kazitds::kazitds.Shop Settings'))

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="{{ route('configurations.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    {{-- Form Display Options --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 text-primary fw-semibold">
                                    <i class="fas fa-sliders-h me-1"></i> @lang('kazitds::kazitds.Sales Form Options')
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" class="form-check-input" id="show_discount_option"
                                           name="show_discount_option" {{ $settings['show_discount_option'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_discount_option">
                                        @lang('kazitds::kazitds.Show Discount Option in Sales Form')
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="show_previous_due_option"
                                           name="show_previous_due_option" {{ $settings['show_previous_due_option'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_previous_due_option">
                                        @lang('kazitds::kazitds.Show Previous Due Option in Sales Form')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pagination Settings --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 text-primary fw-semibold">
                                    <i class="fas fa-list-ol me-1"></i> @lang('kazitds::kazitds.Table Display Settings')
                                </h6>
                            </div>
                            <div class="card-body">
                                <label for="pagination" class="form-label fw-semibold">
                                    @lang('kazitds::kazitds.Results per page')
                                </label>
                                <input type="number" min="1" class="form-control form-control-sm"
                                       id="pagination" name="pagination"
                                       value="{{ old('pagination', $settings['pagination']) }}">
                                <small class="text-muted">
                                    @lang('kazitds::kazitds.Controls how many records will be shown per page')
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Other Settings --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 text-primary fw-semibold">
                                    <i class="fas fa-cog me-1"></i> @lang('kazitds::kazitds.Other Settings')
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" class="form-check-input" id="enable_translation"
                                           name="enable_translation" {{ $settings['enable_translation'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_translation">
                                        @lang('kazitds::kazitds.Enable Translation')
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" class="form-check-input" id="enable_sms"
                                           name="enable_sms" {{ $settings['enable_sms'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_sms">
                                        @lang('kazitds::kazitds.Enable sms')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-encodex px-4">
                        <i class="fas fa-save me-1"></i> @lang('kazitds::kazitds.Save Settings')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
