@php
    // Only the modal that was submitted gets its old input and errors back
    $isSubmitted = old('_modal') === $modalId;
    $value = fn ($field, $default = null) => $isSubmitted ? old($field, $default) : $default;
    $error = fn ($field) => $isSubmitted ? $errors->first($field) : null;
@endphp

<input type="hidden" name="_modal" value="{{ $modalId }}">

<div class="row g-3 purchase-form">
    {{-- Supplier --}}
    <div class="col-md-6">
        <label for="{{ $modalId }}_supplier_id" class="form-label">@lang('kazitds::kazitds.Supplier')</label>
        <select name="supplier_id" id="{{ $modalId }}_supplier_id" class="form-select form-select-sm {{ $error('supplier_id') ? 'is-invalid' : '' }}" data-control="select2-modal">
            <option value="">@lang('kazitds::kazitds.Select Supplier')</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ $value('supplier_id', $purchase->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }} {{ $supplier->company_name ? '('.$supplier->company_name.')' : '' }}
                </option>
            @endforeach
        </select>
        @if($error('supplier_id'))
            <div class="invalid-feedback d-block">{{ $error('supplier_id') }}</div>
        @endif
    </div>

    {{-- Product Variant --}}
    <div class="col-md-6">
        <label for="{{ $modalId }}_product_variant_id" class="form-label">@lang('kazitds::kazitds.Product Variant') <span class="text-danger">*</span></label>
        <select name="product_variant_id" id="{{ $modalId }}_product_variant_id" class="form-select form-select-sm {{ $error('product_variant_id') ? 'is-invalid' : '' }}" data-control="select2-modal" required>
            <option value="">@lang('kazitds::kazitds.Select Product Variant')</option>
            @foreach ($productVariants as $variant)
                <option value="{{ $variant->id }}" {{ $value('product_variant_id', $purchase->product_variant_id ?? $defaultVariantId ?? '') == $variant->id ? 'selected' : '' }}>
                    {{ $variant->product->name }} - {{ $variant->brand->name ?? '--' }} - {{ $variant->pack->name }}
                </option>
            @endforeach
        </select>
        @if($error('product_variant_id'))
            <div class="invalid-feedback d-block">{{ $error('product_variant_id') }}</div>
        @endif
    </div>

    {{-- Quantity --}}
    <div class="col-md-6">
        <label for="{{ $modalId }}_quantity" class="form-label">@lang('kazitds::kazitds.Quantity') <span class="text-danger">*</span></label>
        <input type="number" min="1" class="form-control form-control-sm js-purchase-qty {{ $error('quantity') ? 'is-invalid' : '' }}" id="{{ $modalId }}_quantity" name="quantity"
               value="{{ $value('quantity', $purchase->quantity ?? '') }}" required>
        @if($error('quantity'))
            <div class="invalid-feedback">{{ $error('quantity') }}</div>
        @endif
    </div>

    {{-- Price Per Unit --}}
    <div class="col-md-6">
        <label for="{{ $modalId }}_price_per_unit" class="form-label">@lang('kazitds::kazitds.Price Per Unit') <span class="text-danger">*</span></label>
        <div class="input-group input-group-sm">
            <span class="input-group-text">@lang("kazitds::kazitds.TK")</span>
            <input type="number" step="0.01" min="0" class="form-control js-purchase-price {{ $error('price_per_unit') ? 'is-invalid' : '' }}" id="{{ $modalId }}_price_per_unit" name="price_per_unit"
                   value="{{ $value('price_per_unit', $purchase->price_per_unit ?? '') }}" required>
            @if($error('price_per_unit'))
                <div class="invalid-feedback">{{ $error('price_per_unit') }}</div>
            @endif
        </div>
    </div>

    {{-- Total Price (calculated) --}}
    <div class="col-md-6">
        <label for="{{ $modalId }}_total_price" class="form-label">@lang('kazitds::kazitds.Total Price')</label>
        <div class="input-group input-group-sm">
            <span class="input-group-text">@lang("kazitds::kazitds.TK")</span>
            <input type="text" class="form-control bg-light js-purchase-total" id="{{ $modalId }}_total_price" readonly value="0.00">
        </div>
    </div>

    {{-- Purchase Date --}}
    <div class="col-md-6">
        <label for="{{ $modalId }}_purchase_date" class="form-label">@lang('kazitds::kazitds.Purchase Date') <span class="text-danger">*</span></label>
        <input type="date" class="form-control form-control-sm {{ $error('purchase_date') ? 'is-invalid' : '' }}" id="{{ $modalId }}_purchase_date" name="purchase_date"
               value="{{ $value('purchase_date', isset($purchase) ? $purchase->purchase_date?->format('Y-m-d') : date('Y-m-d')) }}" required>
        @if($error('purchase_date'))
            <div class="invalid-feedback">{{ $error('purchase_date') }}</div>
        @endif
    </div>
</div>
