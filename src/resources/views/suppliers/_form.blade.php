@php
    // Only the modal that was submitted gets its old input and errors back
    $isSubmitted = old('_modal') === $modalId;
    $value = fn ($field, $default = null) => $isSubmitted ? old($field, $default) : $default;
    $error = fn ($field) => $isSubmitted ? $errors->first($field) : null;
@endphp

<input type="hidden" name="_modal" value="{{ $modalId }}">

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_name" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Name') <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <input type="text" class="form-control form-control-sm {{ $error('name') ? 'is-invalid' : '' }}" id="{{ $modalId }}_name" name="name"
               value="{{ $value('name', $supplier->name ?? '') }}" required>
        @if($error('name'))
            <div class="invalid-feedback">{{ $error('name') }}</div>
        @endif
    </div>
</div>

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_company_name" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Company Name')</label>
    <div class="col-sm-9">
        <input type="text" class="form-control form-control-sm {{ $error('company_name') ? 'is-invalid' : '' }}" id="{{ $modalId }}_company_name" name="company_name"
               value="{{ $value('company_name', $supplier->company_name ?? '') }}">
        @if($error('company_name'))
            <div class="invalid-feedback">{{ $error('company_name') }}</div>
        @endif
    </div>
</div>

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_phone" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Phone')</label>
    <div class="col-sm-9">
        <input type="text" class="form-control form-control-sm {{ $error('phone') ? 'is-invalid' : '' }}" id="{{ $modalId }}_phone" name="phone"
               value="{{ $value('phone', $supplier->phone ?? '') }}">
        @if($error('phone'))
            <div class="invalid-feedback">{{ $error('phone') }}</div>
        @endif
    </div>
</div>

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_email" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Email')</label>
    <div class="col-sm-9">
        <input type="email" class="form-control form-control-sm {{ $error('email') ? 'is-invalid' : '' }}" id="{{ $modalId }}_email" name="email"
               value="{{ $value('email', $supplier->email ?? '') }}">
        @if($error('email'))
            <div class="invalid-feedback">{{ $error('email') }}</div>
        @endif
    </div>
</div>

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_address" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Address')</label>
    <div class="col-sm-9">
        <textarea class="form-control form-control-sm {{ $error('address') ? 'is-invalid' : '' }}" id="{{ $modalId }}_address" name="address"
                  rows="1">{{ $value('address', $supplier->address ?? '') }}</textarea>
        @if($error('address'))
            <div class="invalid-feedback">{{ $error('address') }}</div>
        @endif
    </div>
</div>

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_notes" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Notes')</label>
    <div class="col-sm-9">
        <textarea class="form-control form-control-sm {{ $error('notes') ? 'is-invalid' : '' }}" id="{{ $modalId }}_notes" name="notes"
                  rows="1">{{ $value('notes', $supplier->notes ?? '') }}</textarea>
        @if($error('notes'))
            <div class="invalid-feedback">{{ $error('notes') }}</div>
        @endif
    </div>
</div>
