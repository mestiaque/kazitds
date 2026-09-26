@php
    // Only the modal that was submitted gets its old input and errors back
    $isSubmitted = old('_modal') === $modalId;
    $value = fn ($field, $default = null) => $isSubmitted ? old($field, $default) : $default;
    $error = fn ($field) => $isSubmitted ? $errors->first($field) : null;
@endphp

<input type="hidden" name="_modal" value="{{ $modalId }}">

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_name" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Product Name') <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <input type="text" class="form-control form-control-sm {{ $error('name') ? 'is-invalid' : '' }}" id="{{ $modalId }}_name" name="name"
               value="{{ $value('name', $product->name ?? '') }}" required>
        @if($error('name'))
            <div class="invalid-feedback">{{ $error('name') }}</div>
        @endif
    </div>
</div>

{{-- <div class="form-group row mb-3">
    <label for="{{ $modalId }}_description" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Description')</label>
    <div class="col-sm-9">
        <textarea class="form-control {{ $error('description') ? 'is-invalid' : '' }}" id="{{ $modalId }}_description" name="description"
                 rows="3">{{ $value('description', $product->description ?? '') }}</textarea>
        @if($error('description'))
            <div class="invalid-feedback">{{ $error('description') }}</div>
        @endif
    </div>
</div>

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_image" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Product Image')</label>
    <div class="col-sm-9">
        <input type="file" class="form-control {{ $error('image') ? 'is-invalid' : '' }}" id="{{ $modalId }}_image" name="image">
        @if($error('image'))
            <div class="invalid-feedback">{{ $error('image') }}</div>
        @endif

        @if(isset($product) && $product->image)
        <div class="mt-2">
            <img src="{{ route('attachments.show', $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="height: 100px">
        </div>
        @endif
    </div>
</div> --}}
