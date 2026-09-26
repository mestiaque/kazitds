@php
    // Only the modal that was submitted gets its old input and errors back
    $isSubmitted = old('_modal') === $modalId;
    $value = fn ($field, $default = null) => $isSubmitted ? old($field, $default) : $default;
    $error = fn ($field) => $isSubmitted ? $errors->first($field) : null;
@endphp

<input type="hidden" name="_modal" value="{{ $modalId }}">

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_name" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Pack Name') <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <input type="text" class="form-control form-control-sm {{ $error('name') ? 'is-invalid' : '' }}" id="{{ $modalId }}_name" name="name"
               value="{{ $value('name', $pack->name ?? '') }}" required>
        @if($error('name'))
            <div class="invalid-feedback">{{ $error('name') }}</div>
        @endif
    </div>
</div>
