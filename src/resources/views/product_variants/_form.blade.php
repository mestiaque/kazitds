@php
    // Only the modal that was submitted gets its old input and errors back
    $isSubmitted = old('_modal') === $modalId;
    $value = fn ($field, $default = null) => $isSubmitted ? old($field, $default) : $default;
    $error = fn ($field) => $isSubmitted ? $errors->first($field) : null;
    // An unchecked checkbox isn't submitted, so after a failed submit "missing" means off
    $isActive = $isSubmitted ? (bool) old('is_active') : ($productVariant->is_active ?? true);
@endphp

<input type="hidden" name="_modal" value="{{ $modalId }}">

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_product_id" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Product') <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <select name="product_id" id="{{ $modalId }}_product_id" class="form-control {{ $error('product_id') ? 'is-invalid' : '' }}" data-control="select2-modal" required>
            <option value="">@lang('kazitds::kazitds.Select Product')</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" {{ $value('product_id', $productVariant->product_id ?? '') == $product->id ? 'selected' : '' }}>
                    {{ $product->name }}
                </option>
            @endforeach
        </select>
        @if($error('product_id'))
            <div class="invalid-feedback d-block">{{ $error('product_id') }}</div>
        @endif
    </div>
</div>

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_brand_id" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Brand') <span class="text-muted">(@lang('kazitds::kazitds.Optional'))</span></label>
    <div class="col-sm-9">
        <select name="brand_id" id="{{ $modalId }}_brand_id" class="form-control {{ $error('brand_id') ? 'is-invalid' : '' }}" data-control="select2-modal">
            <option value="">@lang('kazitds::kazitds.Select Brand')</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" {{ $value('brand_id', $productVariant->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>
        @if($error('brand_id'))
            <div class="invalid-feedback d-block">{{ $error('brand_id') }}</div>
        @endif
    </div>
</div>

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_pack_id" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Pack') <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <select name="pack_id" id="{{ $modalId }}_pack_id" class="form-control {{ $error('pack_id') ? 'is-invalid' : '' }}" data-control="select2-modal" required>
            <option value="">@lang('kazitds::kazitds.Select Pack')</option>
            @foreach ($packs as $pack)
                <option value="{{ $pack->id }}" {{ $value('pack_id', $productVariant->pack_id ?? '') == $pack->id ? 'selected' : '' }}>
                    {{ $pack->name }}
                </option>
            @endforeach
        </select>
        @if($error('pack_id'))
            <div class="invalid-feedback d-block">{{ $error('pack_id') }}</div>
        @endif
    </div>
</div>

<div class="form-group row mb-3">
    <label for="{{ $modalId }}_is_active" class="col-sm-3 col-form-label">@lang('kazitds::kazitds.Status')</label>
    <div class="col-sm-9">
        <div class="form-check form-switch">
            <input type="checkbox" name="is_active" id="{{ $modalId }}_is_active" class="form-check-input" {{ $isActive ? 'checked' : '' }}>
        </div>
    </div>
</div>
