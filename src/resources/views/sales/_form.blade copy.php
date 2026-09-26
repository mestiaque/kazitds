<div class="card mb-3">
    <div class="card-header bg-primary text-white py-2 d-none">
        <h6 class="m-0 font-weight-bold">@lang('kazitds::kazitds.Customer Information')</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-1">
                <label for="customer_id">@lang('kazitds::kazitds.Customer')</label>
                <select id="customer_id" name="customer_id" class="form-select form-select-sm">
                    <option value="">@lang('kazitds::kazitds.Select Customer')</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}"
                            data-name="{{ $customer->name }}"
                            data-phone="{{ $customer->phone }}"
                            {{ isset($sale) && $sale->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} - {{ $customer->phone }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-1">
                <label for="customer_name">@lang('kazitds::kazitds.Customer Name')</label>
                <input type="text" class="form-control form-control-sm" id="customer_name" name="customer_name"
                    value="{{ $sale->customer_name ?? old('customer_name', '') }}">
            </div>
            <div class="col-md-4 mb-1">
                <label for="mobile_number">@lang('kazitds::kazitds.Mobile Number')</label>
                <input type="text" class="form-control form-control-sm" id="mobile_number" name="mobile_number"
                    value="{{ $sale->mobile_number ?? old('mobile_number', '') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-1">
                <label for="invoice_number">@lang('kazitds::kazitds.Invoice Number')</label>
                <input type="text" class="form-control form-control-sm" id="invoice_number" value="{{ $sale->invoice_number ?? $invoiceNumber ?? '' }}" readonly>
            </div>
            <div class="col-md-4 mb-1">
                <label for="sale_date">@lang('kazitds::kazitds.Sale Date') <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-sm @error('sale_date') is-invalid @enderror" id="sale_date" name="sale_date"
                    value="{{ $sale->sale_date ?? old('sale_date', date('Y-m-d')) }}" required>
                @error('sale_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="card mb-3 border border-primary">
    <div class="card-header bg-primary text-white py-1 d-none">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-white">@lang('kazitds::kazitds.Sale Items')</h6>
            </div>
            <div class="col-auto text-end">
                <button type="button" class="btn btn-sm btn-light" id="add_items">
                    <i class="fas fa-plus"></i> @lang('kazitds::kazitds.Add')
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0 ">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 table-sm table-encodex" id="items_table">
                <thead class="bg-light">
                    <tr>
                        <th width="{{ ($showDiscount ?? true) ? '30%' : '35%' }}">@lang('kazitds::kazitds.Product')</th>
                        <th width="10%">@lang('kazitds::kazitds.Stock')</th>
                        <th width="10%">@lang('kazitds::kazitds.Quantity')</th>
                        <th width="15%">@lang('kazitds::kazitds.Unit') @lang('kazitds::kazitds.Price')</th>
                        @if($showDiscount ?? true)
                        <th width="15%">@lang('kazitds::kazitds.Discount')</th>
                        @endif
                        <th width="15%">@lang('kazitds::kazitds.Total')</th>
                        <th width="5%" class="text-end">
                            <button type="button" class="btn btn-sm btn-success" title="@lang('kazitds::kazitds.Add Item')" id="add_item">
                                <i class="fas fa-plus"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($sale) && $sale->items->count() > 0)
                        @foreach($sale->items as $index => $item)
                            <tr class="item-row">
                                <td>
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    <select name="items[{{ $index }}][product_variant_id]" class="form-control form-control-sm product-select" data-row="{{ $index }}" required>
                                        @foreach($productVariants as $variant)
                                            @php
                                                $stockQty = $variant->id == $item->product_variant_id
                                                    ? $variant->stock
                                                    : $variant->getCurrentStock();
                                            @endphp
                                            <option value="{{ $variant->id }}"
                                                data-price="{{ $variant->selling_price }}"
                                                data-stock="{{ $stockQty }}"
                                                {{ $item->product_variant_id == $variant->id ? 'selected' : '' }}>
                                                {{ $variant->product->name }} - {{ $variant->brand->name?? '--' }} - {{ $variant->pack->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    <span class="stock-display badge bg-info mt-1">{{ $item->productVariant->getCurrentStock() + $item->quantity }}</span>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control form-control-sm quantity" min="1"
                                        value="{{ $item->quantity ?? '' }}" data-row="{{ $index }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $index }}][price_per_unit]" class="form-control form-control-sm price text-end"
                                        value="{{ $item->price_per_unit ?? '' }}" data-row="{{ $index }}" required>
                                </td>
                                @if($showDiscount ?? true)
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $index }}][item_discount]" class="form-control form-control-sm item-discount text-end"
                                        value="{{ $item->item_discount ?? '' }}" min="0" data-row="{{ $index }}">
                                </td>
                                @else
                                <input type="hidden" name="items[{{ $index }}][item_discount]" value="0">
                                @endif
                                <td>
                                    <input type="text" class="form-control form-control-sm item-total text-end" value="{{ $item->total_price }}" readonly>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-danger remove-row float-end" title="@lang("kazitds::kazitds.Remove Item")"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="item-row">
                            <td>
                                <select name="items[0][product_variant_id]" class="form-control form-control-sm product-select" data-row="0" required>
                                    <option value="">@lang('kazitds::kazitds.Select Product')</option>
                                    @foreach($productVariants as $variant)
                                        <option value="{{ $variant->id }}" data-price="{{ $variant->selling_price }}" data-stock="{{ $variant->stock }}">
                                            {{ $variant->product->name }} - {{ $variant->brand->name ?? '--' }} - {{ $variant->pack->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-center">
                                <span class="stock-display badge bg-info mt-1">0</span>
                            </td>
                            <td>
                                <input type="number" name="items[0][quantity]" class="form-control form-control-sm quantity" value="" min="1" data-row="0" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[0][price_per_unit]" class="form-control form-control-sm price text-end" value="" data-row="0" required>
                            </td>
                            @if($showDiscount ?? true)
                            <td>
                                <input type="number" step="0.01" name="items[0][item_discount]" class="form-control form-control-sm item-discount text-end" value="" min="0" data-row="0">
                            </td>
                            @else
                            <input type="hidden" name="items[0][item_discount]" value="0">
                            @endif
                            <td>
                                <input type="text" class="form-control form-control-sm item-total text-end" value="0.00" readonly>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-danger remove-row float-end" title="@lang("kazitds::kazitds.Remove Item")"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-3 ">
    <div class="card-header bg-primary text-white py-2 d-none ">
        <h6 class="m-0 font-weight-bold">@lang('kazitds::kazitds.Sale Summary')</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="notes">@lang('kazitds::kazitds.Notes')</label>
                    <textarea name="notes" id="notes" class="form-control form-control-sm" rows="3">{{ $sale->notes ?? old('notes', '') }}</textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group row mb-1">
                    <label for="subtotal" class="col-sm-4 col-form-label">@lang('kazitds::kazitds.Subtotal')</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm text-end" id="subtotal" value="0.00" readonly>
                    </div>
                </div>

                @if($showDiscount ?? true)
                <div class="form-group row mb-1">
                    <label for="discount" class="col-sm-4 col-form-label">@lang('kazitds::kazitds.Discount')</label>
                    <div class="col-sm-8">
                        <input type="number" step="0.01" class="form-control form-control-sm text-end" id="discount" name="discount"
                            value="{{ $sale->discount ?? old('discount', '') }}">
                    </div>
                </div>
                @endif

                @if($showPreviousDue ?? true)
                <div class="form-group row mb-1">
                    <label for="previous_due" class="col-sm-4 col-form-label">@lang('kazitds::kazitds.Previous Due')</label>
                    <div class="col-sm-8">
                        <input type="number" step="0.01" class="form-control form-control-sm text-end" id="previous_due" name="previous_due"
                            value="{{ $sale->previous_due ?? old('previous_due', '') }}">
                    </div>
                </div>
                @endif

                <div class="form-group row">
                    <label for="grand_total" class="col-sm-4 col-form-label fw-bold">@lang('kazitds::kazitds.Grand Total')</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm text-end fw-bold" id="grand_total" value="0.00" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center mb-4">
    <button type="submit" class="btn btn-encodex btn-lg px-5">
        <i class="fas fa-save me-1"></i> {{ isset($sale) ? __('kazitds::kazitds.Update Sale') : __('kazitds::kazitds.Complete Sale') }}
    </button>
    <a href="{{ route('sales.index') }}" class="btn btn-encodex-cancel btn-lg px-5 ms-2">
        <i class="fas fa-times me-1"></i> @lang('kazitds::kazitds.Cancel')
    </a>
</div>

@push('js')
<script>
    $(document).ready(function() {
        // Initialize Select2
        initializeSelect2();

        // Track selected product variants
        let selectedVariants = [];

        // Initialize tracking of selected products
        $('.product-select').each(function() {
            const variantId = $(this).val();
            if (variantId) {
                selectedVariants.push(variantId);
            }
        });

        // Handle customer selection
        $('#customer_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                $('#customer_name').val(selectedOption.data('name'));
                $('#mobile_number').val(selectedOption.data('phone'));
            } else {
                $('#customer_name').val('');
                $('#mobile_number').val('');
            }
        });

        // Add new item row
        $('#add_item').on('click', function() {
            const rowIndex = $('.item-row').length;

            let html = `
                <tr class="item-row">
                    <td>
                        <select name="items[${rowIndex}][product_variant_id]" class="form-select form-select-sm product-select" data-row="${rowIndex}" required>
                            <option value="">@lang('kazitds::kazitds.Select Product')</option>
                            @foreach($productVariants as $variant)
                                <option value="{{ $variant->id }}" data-price="{{ $variant->selling_price }}" data-stock="{{ $variant->stock }}">
                                    {{ $variant->product->name }} - {{ $variant->brand->name ?? '--' }} - {{ $variant->pack->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center">
                        <span class="stock-display badge bg-info mt-1">0</span>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][quantity]" class="form-control form-control-sm quantity" value="" min="1" data-row="${rowIndex}" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[${rowIndex}][price_per_unit]" class="form-control form-control-sm price text-end" value="" data-row="${rowIndex}" required>
                    </td>
                    @if($showDiscount ?? true)
                    <td>
                        <input type="number" step="0.01" name="items[${rowIndex}][item_discount]" class="form-control form-control-sm item-discount text-end" value="" min="0" data-row="${rowIndex}">
                    </td>
                    @else
                    <input type="hidden" name="items[${rowIndex}][item_discount]" value="0">
                    @endif
                    <td>
                        <input type="text" class="form-control form-control-sm item-total text-end" value="0.00" readonly>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-danger remove-row float-end" title="@lang("kazitds::kazitds.Remove Item")"><i class="fas fa-times"></i></button>
                    </td>
                </tr>
            `;

            $('#items_table tbody').append(html);
            initializeSelect2();

            // Hide already selected options in the new dropdown
            const newSelect = $(`select[name="items[${rowIndex}][product_variant_id]"]`);
            updateProductOptions(newSelect);
        });

        // Remove item row
        $(document).on('click', '.remove-row', function() {
            if ($('.item-row').length > 1) {
                const row = $(this).closest('tr');
                const variantId = row.find('.product-select').val();

                if (variantId) {
                    const index = selectedVariants.indexOf(variantId);
                    if (index > -1) {
                        selectedVariants.splice(index, 1);
                    }
                }

                row.remove();
                renumberRows();
                calculateTotals();

                $('.product-select').each(function() {
                    updateProductOptions($(this));
                });
            } else {
                alert('@lang("kazitds::kazitds.At least one item is required.")');
            }
        });

        // When product variant changes, update price, check stock and track selection
        $(document).on('change', '.product-select', function() {
            const row = $(this).data('row');
            const selectedOption = $(this).find('option:selected');
            const previousValue = $(this).attr('data-previous-value');
            const newValue = selectedOption.val();

            if (previousValue) {
                const prevIndex = selectedVariants.indexOf(previousValue);
                if (prevIndex > -1) {
                    selectedVariants.splice(prevIndex, 1);
                }
            }

            if (newValue) {
                if (selectedVariants.includes(newValue)) {
                    alert('@lang("kazitds::kazitds.This product is already added to the sale. Please select a different product.")');
                    $(this).val('').trigger('change.select2');
                    return;
                }

                selectedVariants.push(newValue);
                $(this).attr('data-previous-value', newValue);

                const price = selectedOption.data('price');
                const stock = selectedOption.data('stock');

                $(`input[name="items[${row}][price_per_unit]"]`).val(parseFloat(price).toFixed(2));
                $(this).closest('tr').find('.stock-display').text(stock);

                $(`input[name="items[${row}][quantity]"]`).attr('max', stock);

                calculateRowTotal(row);

                $('.product-select').not(this).each(function() {
                    updateProductOptions($(this));
                });
            } else {
                $(`input[name="items[${row}][price_per_unit]"]`).val('');
                $(this).closest('tr').find('.stock-display').text(0);
                calculateRowTotal(row);
            }
        });

        // Helper function to update product dropdown options
        function updateProductOptions(select) {
            const currentValue = select.val();

            select.find('option').each(function() {
                const optionValue = $(this).val();
                if (optionValue && optionValue !== currentValue && selectedVariants.includes(optionValue)) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });

            select.select2({
                placeholder: "@lang('kazitds::kazitds.Select Product')",
                width: '100%',
                dropdownParent: $('#items_table')
            }).on('select2:select', function() {
                const row = $(this).data('row');
                const selectedOption = $(this).find('option:selected');

                if (selectedOption.val()) {
                    const price = selectedOption.data('price');
                    $(`input[name="items[${row}][price_per_unit]"]`).val(parseFloat(price).toFixed(2));
                    calculateRowTotal(row);
                }
            });
        }

        $('.product-select').each(function() {
            updateProductOptions($(this));
        });

        $(document).on('input', '.quantity, .price, .item-discount', function() {
            const row = $(this).data('row');
            if ($(this).hasClass('quantity')) {
                const quantity = parseInt($(this).val()) || 0;
                const stock = parseInt($(this).closest('tr').find('.stock-display').text()) || 0;

                if (quantity > stock) {
                    alert('@lang("kazitds::kazitds.Quantity cannot exceed available stock.")');
                    $(this).val(stock);
                }
            }
            calculateRowTotal(row);
        });

        $('#discount, #previous_due').on('input', function() {
            calculateTotals();
        });

        function calculateRowTotal(row) {
            const quantity = parseFloat($(`input[name="items[${row}][quantity]"]`).val()) || 0;
            const price = parseFloat($(`input[name="items[${row}][price_per_unit]"]`).val()) || 0;
            @if($showDiscount ?? true)
            const discount = parseFloat($(`input[name="items[${row}][item_discount]"]`).val()) || 0;
            @else
            const discount = 0;
            @endif

            const total = (quantity * price) - discount;
            $('.item-row').eq(row).find('.item-total').val(total.toFixed(2));

            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0;
            $('.item-total').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });

            const discount = parseFloat($('#discount').val()) || 0;
            const previousDue = parseFloat($('#previous_due').val()) || 0;
            const grandTotal = subtotal - discount + previousDue;

            $('#subtotal').val(subtotal.toFixed(2));
            $('#grand_total').val(grandTotal.toFixed(2));
        }

        function renumberRows() {
            $('.item-row').each(function(index) {
                const row = $(this);

                row.find('.product-select').attr('data-row', index).attr('name', `items[${index}][product_variant_id]`);
                row.find('.quantity').attr('data-row', index).attr('name', `items[${index}][quantity]`);
                row.find('.price').attr('data-row', index).attr('name', `items[${index}][price_per_unit]`);
                row.find('.item-discount').attr('data-row', index).attr('name', `items[${index}][item_discount]`);

                const idField = row.find('input[name^="items"][name$="[id]"]');
                if (idField.length) {
                    idField.attr('name', `items[${index}][id]`);
                }
            });
        }

        function initializeSelect2() {
            $('.product-select').select2({
                placeholder: "@lang('kazitds::kazitds.Select Product')",
                width: '100%',
                dropdownParent: $('#items_table')
            });

            $('#customer_id').select2({
                placeholder: "@lang('kazitds::kazitds.Select Customer')",
                width: '100%',
                allowClear: false,
                dropdownParent: $('body')
            });
        }

        calculateTotals();
    });
</script>
@endpush

@push('css')
<style>


</style>
@endpush
