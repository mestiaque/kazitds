<div class="row g-3">
    {{-- Supplier --}}
    <div class="col-md-6">
        <label for="supplier_id" class="form-label">@lang('kazitds::kazitds.Supplier')</label>
        <select name="supplier_id" id="supplier_id" class="form-select form-select-sm @error('supplier_id') is-invalid @enderror" data-control="select2">
            <option value="">@lang('kazitds::kazitds.Select Supplier')</option>
            @foreach ($suppliers ?? [] as $supplier)
                <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchase->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }} {{ $supplier->company_name ? '('.$supplier->company_name.')' : '' }}
                </option>
            @endforeach
        </select>
        @error('supplier_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Product Variant --}}
    <div class="col-md-6">
        <label for="product_variant_id" class="form-label">@lang('kazitds::kazitds.Product Variant') <span class="text-danger">*</span></label>
        <select name="product_variant_id" id="product_variant_id" class="form-select form-select-sm @error('product_variant_id') is-invalid @enderror" data-control="select2" required>
            <option value="">@lang('kazitds::kazitds.Select Product Variant')</option>
            @foreach ($productVariants as $variant)
                <option value="{{ $variant->id }}"
                        data-price="{{ $variant->selling_price }}"
                        {{ old('product_variant_id', $purchase->product_variant_id ?? '') == $variant->id ? 'selected' : '' }}>
                    {{ $variant->product->name }} - {{ $variant->brand->name ?? '--' }} - {{ $variant->pack->name }}
                </option>
            @endforeach
        </select>
        @error('product_variant_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Quantity --}}
    <div class="col-md-6">
        <label for="quantity" class="form-label">@lang('kazitds::kazitds.Quantity') <span class="text-danger">*</span></label>
        <input type="number" min="1" class="form-control form-control-sm @error('quantity') is-invalid @enderror" id="quantity" name="quantity"
               value="{{ old('quantity', $purchase->quantity ?? 0) }}" required>
        @error('quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Price Per Unit --}}
    <div class="col-md-6">
        <label for="price_per_unit" class="form-label">@lang('kazitds::kazitds.Price Per Unit') <span class="text-danger">*</span></label>
        <div class="input-group input-group-sm">
            <span class="input-group-text">@lang("kazitds::kazitds.TK")</span>
            <input type="number" step="0.01" class="form-control @error('price_per_unit') is-invalid @enderror" id="price_per_unit" name="price_per_unit"
                   value="{{ old('price_per_unit', $purchase->price_per_unit ?? '') }}" required>
            @error('price_per_unit')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Total Price --}}
    <div class="col-md-6">
        <label for="total_price" class="form-label">@lang('kazitds::kazitds.Total Price')</label>
        <div class="input-group input-group-sm">
            <span class="input-group-text">@lang("kazitds::kazitds.TK")</span>
            <input type="text" class="form-control bg-light" id="total_price" readonly
                   value="{{ old('total_price', $purchase->total_price ?? 0) }}">
        </div>
    </div>

    {{-- Purchase Date --}}
    <div class="col-md-6">
        <label for="purchase_date" class="form-label">@lang('kazitds::kazitds.Purchase Date') <span class="text-danger">*</span></label>
        <input type="date" class="form-control form-control-sm @error('purchase_date') is-invalid @enderror" id="purchase_date" name="purchase_date"
               value="{{ old('purchase_date', isset($purchase) ? $purchase->purchase_date : date('Y-m-d')) }}" required>
        @error('purchase_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-group row mt-3">
    <div class="col-sm-9 offset-sm-3 text-end">
        <button type="submit" class="btn btn-sm btn-encodex">
            {{ isset($purchase) ? __('kazitds::kazitds.Update Purchase') : __('kazitds::kazitds.Create Purchase') }}
        </button>
        <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-encodex-cancel">@lang('kazitds::kazitds.Cancel')</a>
    </div>
</div>


@push('js')
<script>
    $(document).ready(function() {
        $('#quantity, #price_per_unit').on('input', function() {
            calculateTotal();
        });

        function calculateTotal() {
            let quantity = parseFloat($('#quantity').val()) || 0;
            let price = parseFloat($('#price_per_unit').val()) || 0;
            let total = quantity * price;
            $('#total_price').val(total.toFixed(2));
        }
        calculateTotal();
    });
</script>
@endpush
