@extends('me::master')

@section('title', trans('kazitds::kazitds.Shop Settings'))

@section('content')
<div class="container-fluids">
    <div class="card shadow mb-4 w-100">
        <div class="card-body">
            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">
                                <i class="fas fa-store me-1"></i> @lang('kazitds::kazitds.Shop Name') <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="shop_name" class="form-control form-control-sm @error('shop_name') is-invalid @enderror"
                                   value="{{ old('shop_name', $settings['shop_name']) }}" required>
                            @error('shop_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">
                                <i class="fas fa-map-marker-alt me-1"></i> @lang('kazitds::kazitds.Shop Address')
                            </label>
                            <textarea name="shop_address" class="form-control form-control-sm @error('shop_address') is-invalid @enderror"
                                      rows="3">{{ old('shop_address', $settings['shop_address']) }}</textarea>
                            @error('shop_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">
                                <i class="fas fa-envelope me-1"></i> @lang('kazitds::kazitds.Email Address')
                            </label>
                            <input type="email" name="shop_email" class="form-control form-control-sm @error('shop_email') is-invalid @enderror"
                                   value="{{ old('shop_email', $settings['shop_email']) }}">
                            @error('shop_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">
                                <i class="fas fa-phone me-1"></i> @lang('kazitds::kazitds.Phone Number')
                            </label>
                            <input type="text" name="shop_phone" class="form-control form-control-sm @error('shop_phone') is-invalid @enderror"
                                   value="{{ old('shop_phone', $settings['shop_phone']) }}">
                            @error('shop_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-primary">
                                <i class="fas fa-exclamation-triangle me-1"></i> @lang('kazitds::kazitds.Low Stock Warning')
                            </label>
                            <div class="input-group">
                                <input type="number" name="low_stock_threshold" min="1"
                                       class="form-control form-control-sm @error('low_stock_threshold') is-invalid @enderror"
                                       value="{{ old('low_stock_threshold', $settings['low_stock_threshold']) }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">@lang('kazitds::kazitds.Items')</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">@lang('kazitds::kazitds.Products with stock below this number will be marked as "Low Stock"')</small>
                            @error('low_stock_threshold')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(get_setting('enable_sms'))
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="sms_notifications[create]" id="sms_create" class="form-check-input"
                                        value="1" {{ old('sms_notifications.create', $settings['sms_permit']['create'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_create">@lang('kazitds::kazitds.Send SMS for new sales')</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="sms_notifications[edit]" id="sms_edit" class="form-check-input"
                                        value="1" {{ old('sms_notifications.edit', $settings['sms_permit']['edit'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_edit">@lang('kazitds::kazitds.Send SMS for edited sales')</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="sms_notifications[payment]" id="sms_payment" class="form-check-input"
                                        value="1" {{ old('sms_notifications.payment', $settings['sms_permit']['payment'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_payment">@lang('kazitds::kazitds.Send SMS for payment notifications')</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="sms_notifications[reminder]" id="sms_reminder" class="form-check-input"
                                        value="1" {{ old('sms_notifications.reminder', $settings['sms_permit']['reminder'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_reminder">@lang('kazitds::kazitds.Send SMS for reminder notifications')</label>
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="col-md-6">
                        <div class="form-group text-center">
                            <label class="font-weight-bold text-primary d-block">
                                <i class="fas fa-image me-1"></i> @lang('kazitds::kazitds.Shop Logo')
                            </label>

                            <div class="logo-preview mb-3">
                                @if($settings['shop_logo'])
                                    <img src="{{ route('shop_logo.show', $settings['shop_logo']) }}"
                                         alt="Shop Logo" class="img-thumbnail" style="max-height: 200px;">
                                @else
                                    <div class="empty-logo p-4 bg-light text-center border rounded">
                                        <i class="fas fa-image fa-3x text-gray-400"></i>
                                        <p class="mt-2 text-gray-500">@lang('kazitds::kazitds.No logo uploaded')</p>
                                    </div>
                                @endif
                            </div>

                            <div class="custom-file">
                                <input type="file" class="border border-primary custom-file-input @error('shop_logo') is-invalid @enderror"
                                       id="shop_logo" name="shop_logo" accept="image/*">
                                {{-- <label class="custom-file-label" for="shop_logo">@lang('kazitds::kazitds.Choose image')</label> --}}
                                @error('shop_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">@lang('kazitds::kazitds.Recommended size: 300x150 pixels, Max: 2MB')</small>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-encodex px-4">
                        <i class="fas fa-save me-1"></i> @lang('kazitds::kazitds.Save Settings')
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
        // Show image name in file input
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);

            // Show image preview
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('.logo-preview').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-height: 200px;">');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endpush

@push('css')
<style>
    .custom-file-label::after {
        content: "@lang('kazitds::kazitds.Browse')";
    }

    .empty-logo {
        border: 2px dashed #ddd;
        border-radius: 5px;
    }
</style>
@endpush
