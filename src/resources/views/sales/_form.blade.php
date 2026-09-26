<div class="card mb-3">
    <div class="card-header bg-primary text-white py-2 d-none">
        <h6 class="m-0 font-weight-bold">@lang('kazitds::kazitds.Customer Information')</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-1">
                <label for="customer_id">@lang('kazitds::kazitds.Customer')</label>
                @if(isset($sale))
                    <select id="customer_id" name="customer_id" class="form-select form-select-sm" required>
                        <option value="">@lang('kazitds::kazitds.Select Customer')</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                data-name="{{ $customer->name }}"
                                data-phone="{{ $customer->phone }}"
                                data-prevdue="{{ $customer->due_amount }}"
                                {{ old('customer_id', $sale->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} - {{ $customer->phone }}
                            </option>
                        @endforeach
                    </select>
                    @if(!$sale->customer)
                        <small class="text-danger">@lang('kazitds::kazitds.This sale has no customer. Please select one to continue.')</small>
                    @endif
                @else
                    <select id="customer_id" name="customer_id" class="form-select form-select-sm">
                        <option value="">@lang('kazitds::kazitds.Select Customer')</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                data-name="{{ $customer->name }}"
                                data-phone="{{ $customer->phone }}"
                                data-prevdue="{{ $customer->due_amount }}"
                                {{ isset($sale) && $sale->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} - {{ $customer->phone }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
            @if(!isset($sale))
                <div class="col-md-4 mb-1">
                    <label for="customer_name">@lang('kazitds::kazitds.Customer Name')</label>
                    <input type="text" class="form-control form-control-sm" id="customer_name" name="customer_name"
                        value="{{ $sale->customer_name ?? old('customer_name', '') }}" required>
                </div>
                <div class="col-md-4 mb-1">
                    <label for="mobile_number">@lang('kazitds::kazitds.Mobile Number')</label>
                    <input type="text" class="form-control form-control-sm" id="mobile_number" name="mobile_number"
                        value="{{ $sale->mobile_number ?? old('mobile_number', '') }}">
                </div>
            @endif
            <div class="col-md-4 mb-1">
                <label for="invoice_number">@lang('kazitds::kazitds.Invoice Number')</label>
                <input type="text" class="form-control form-control-sm" id="invoice_number" value="{{ $sale->invoice_number ?? $invoiceNumber ?? '' }}" readonly>
            </div>
            <div class="col-md-4 mb-1">
                <label for="sale_date">@lang('kazitds::kazitds.Sale Date') <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-sm @error('sale_date') is-invalid @enderror" id="sale_date" name="sale_date"
                    value={{ old('sale_date', isset($sale->sale_date) ? $sale->sale_date->format('Y-m-d') : date('Y-m-d')) }} required>
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
                        <!-- Removed item-wise discount column -->
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
                                <!-- Removed item-wise discount input -->
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
                            <!-- Removed item-wise discount input -->
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

<div class="card mb-3">
    <div class="card-header bg-primary text-white py-2 d-none">
        <h6 class="m-0 font-weight-bold">@lang('kazitds::kazitds.Sale Summary')</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="notes">@lang('kazitds::kazitds.Notes')</label>
                    <textarea name="notes" id="notes" class="form-control form-control-sm" rows="3">{{ old('notes', $sale?->notes ?? '') }}</textarea>
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
                            value="{{ old('discount', $sale?->discount ?? 0) }}">
                    </div>
                </div>
                @endif

                @if($showPreviousDue ?? true)
                <div class="form-group row mb-1">
                    <label for="previous_due" class="col-sm-4 col-form-label text-encodex-secondary">@lang('kazitds::kazitds.Previous Due')</label>
                    <div class="col-sm-8">
                        <input type="number" step="0.01" class="form-control form-control-sm text-end text-encodex-secondary" id="previous_due" name="previous_due"
                            value="{{ old('previous_due', $dues?->previous_due ?? 0) }}" readonly>
                    </div>
                </div>
                @endif

                <div class="form-group row">
                    <label for="grand_total" class="col-sm-4 col-form-label fw-bold">@lang('kazitds::kazitds.Grand Total')</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm text-end fw-bold" id="grand_total" value="0.00" readonly>
                    </div>
                </div>

                <div class="form-group row mb-1">
                    <label for="payment" class="col-sm-4 col-form-label text-success">@lang('kazitds::kazitds.Payment Amount')</label>
                    <div class="col-sm-8">
                        <input type="number" step="0.01" class="form-control form-control-sm text-end text-success" id="payment" name="payment"
                            value="{{ old('payment', $dues?->paid_amount ?? 0) }}">
                    </div>
                </div>

                <div class="form-group row mb-1">
                    <label for="due_after_payment" class="col-sm-4 col-form-label text-danger">@lang('kazitds::kazitds.Due After Payment')</label>
                    <div class="col-sm-8">
                        <input type="number" step="0.01" class="form-control form-control-sm text-end text-danger" id="due_after_payment" name="due_after_payment"
                            value="{{ old('due_after_payment', $dues?->total_due ?? 0) }}" readonly>
                    </div>
                </div>

                <div class="form-group row mb-1">
                    <label for="sms" class="col-sm-4 col-form-label text-warning">@lang('kazitds::kazitds.Send SMS')</label>
                    <div class="col-sm-8">
                        <input type="checkbox" class="form-check-input" style="vertical-align: bottom; width:1.5rem !important; height:1.5rem !important" id="sms" name="sms" value="1" {{ old('sms', 0) ? 'checked' : '' }}>
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
    // Final cleanup before form submit: remove orphaned item fields
    $('form').on('submit', function() {
        $('#items_table tbody input, #items_table tbody select').each(function() {
            if (!$(this).closest('tr').hasClass('item-row')) {
                $(this).remove();
            }
        });
    });
$(document).ready(function() {
    // Initialize Select2 for product and customer selects
    initializeSelect2();

    const isEditMode = {{ isset($sale) ? 'true' : 'false' }};
    let initialCustomerId = $('#customer_id').val() || '';
    let revertingCustomerChange = false;

    // Track selected product variants to prevent duplicates
    let selectedVariants = [];

    // Initialize selected variants from existing rows
    $('.product-select').each(function() {
        const variantId = $(this).val();
        if (variantId) {
            selectedVariants.push(variantId);
        }
    });

    // Customer selection: fill name, phone, previous due, disable fields
    $('#customer_id').on('change', function() {
        const selectedCustomerId = $(this).val() || '';

        if (!revertingCustomerChange && isEditMode && initialCustomerId && selectedCustomerId !== initialCustomerId) {
            const shouldChangeCustomer = confirm('@lang("kazitds::kazitds.Customer পরিবর্তন করলে আগের ও নতুন customer এর ledger/due আবার হিসাব হবে। আপনি কি customer change করতে চান?")');

            if (!shouldChangeCustomer) {
                revertingCustomerChange = true;
                $(this).val(initialCustomerId).trigger('change');
                return;
            }
        }

        if (revertingCustomerChange) {
            revertingCustomerChange = false;
        }

        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            $('#customer_name').val(selectedOption.data('name')).prop('disabled', true);
            $('#mobile_number').val(selectedOption.data('phone')).prop('disabled', true);
            $('#previous_due').val(selectedOption.data('prevdue')).prop('disabled', true);
        } else {
            $('#customer_name').val('').prop('disabled', false);
            $('#mobile_number').val('').prop('disabled', false);
            $('#previous_due').val('').prop('disabled', false);
        }

        calculateTotals();
        const grandTotal = parseFloat($('#grand_total').val()) || 0;
        const payment = parseFloat($('#payment').val()) || 0;
        $('#due_after_payment').val((grandTotal - payment).toFixed(2));
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
                if (index > -1) selectedVariants.splice(index, 1);
            }
            // Remove the entire row and all its fields
            row.remove();
            // After removal, check for orphaned fields and clean up
            // Remove any input/select fields that are not inside .item-row
            $('#items_table tbody').find('input, select').each(function() {
                if (!$(this).closest('tr').hasClass('item-row')) {
                    $(this).remove();
                }
            });
            renumberRows();
            calculateTotals();
            $('.product-select').each(function() {
                updateProductOptions($(this));
            });
        } else {
            alert('@lang("kazitds::kazitds.At least one item is required.")');
        }
    });

    // Product change: fill price, stock, prevent duplicate, handle NaN
    $(document).on('change', '.product-select', function() {
        const row = $(this).data('row');
        const selectedOption = $(this).find('option:selected');
        const previousValue = $(this).attr('data-previous-value');
        const newValue = selectedOption.val();

        // Remove previous selection
        if (previousValue) {
            const prevIndex = selectedVariants.indexOf(previousValue);
            if (prevIndex > -1) selectedVariants.splice(prevIndex, 1);
        }

        if (newValue) {
            // Prevent duplicate selection
            if (selectedVariants.includes(newValue)) {
                alert('@lang("kazitds::kazitds.This product is already added to the sale. Please select a different product.")');
                $(this).val('').trigger('change.select2');
                return;
            }

            selectedVariants.push(newValue);
            $(this).attr('data-previous-value', newValue);

            // Read data-price and data-stock with fallback
            let price = selectedOption.data('price');
            price = (typeof price !== 'undefined' && price !== "" && !isNaN(price)) ? parseFloat(price) : "";
            $(`input[name="items[${row}][price_per_unit]"]`).val(price !== "" ? price.toFixed(2) : '');

            let stock = selectedOption.data('stock');
            stock = (typeof stock !== 'undefined' && stock !== "" && !isNaN(stock)) ? parseInt(stock) : 0;
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

    // Update product dropdown options (disable already selected)
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
            let price = selectedOption.data('price');
            price = (typeof price !== 'undefined' && price !== "" && !isNaN(price)) ? parseFloat(price) : "";
            $(`input[name="items[${row}][price_per_unit]"]`).val(price !== "" ? price.toFixed(2) : '');
            calculateRowTotal(row);
        });
    }

    // Initial update for all product selects
    $('.product-select').each(function() {
        updateProductOptions($(this));
    });

    // Quantity, price, discount change
    $(document).on('input', '.quantity, .price', function() {
        const row = $(this).data('row');
        if ($(this).hasClass('quantity')) {
            let quantity = parseInt($(this).val()) || 0;
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

    // Calculate row total
    function calculateRowTotal(row) {
        const quantity = parseFloat($(`input[name="items[${row}][quantity]"]`).val()) || 0;
        const price = parseFloat($(`input[name="items[${row}][price_per_unit]"]`).val()) || 0;
        // No item-wise discount
        const total = (quantity * price);
        $('.item-row').eq(row).find('.item-total').val(isNaN(total) ? '0.00' : total.toFixed(2));
        calculateTotals();
    }

    // Calculate subtotal, grand total, and due after payment
    function calculateTotals() {
        let subtotal = 0;
        $('.item-total').each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });
        const discount = parseFloat($('#discount').val()) || 0;
        const previousDue = parseFloat($('#previous_due').val()) || 0;
        const grandTotal = subtotal - discount + previousDue;
        $('#subtotal').val(isNaN(subtotal) ? '0.00' : subtotal.toFixed(2));
        $('#grand_total').val(isNaN(grandTotal) ? '0.00' : grandTotal.toFixed(2));

        const payment = parseFloat($('#payment').val()) || 0;
        const dueAfterPayment = grandTotal - payment;
        $('#due_after_payment').val(isNaN(dueAfterPayment) ? '0.00' : dueAfterPayment.toFixed(2));
    }

    // Renumber form element names after row removal
    function renumberRows() {
        $('.item-row').each(function(index) {
            const row = $(this);
            row.find('.product-select').attr('data-row', index).attr('name', `items[${index}][product_variant_id]`);
            row.find('.quantity').attr('data-row', index).attr('name', `items[${index}][quantity]`);
            row.find('.price').attr('data-row', index).attr('name', `items[${index}][price_per_unit]`);
            // Removed: row.find('.item-discount').attr('data-row', index).attr('name', `items[${index}][item_discount]`);
            const idField = row.find('input[name^="items"][name$="[id]"]');
            if (idField.length) {
                idField.attr('name', `items[${index}][id]`);
            }
        });
        // Cleanup: remove any orphaned item fields not inside .item-row
        $('#items_table tbody input, #items_table tbody select').each(function() {
            if (!$(this).closest('tr').hasClass('item-row')) {
                $(this).remove();
            }
        });
    }

    // Initialize Select2 on all product and customer selects
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

    // Initial totals calculation
    calculateTotals();

    // Payment input: update due after payment
    $('#payment').on('input', function() {
        const grandTotal = parseFloat($('#grand_total').val()) || 0;
        const payment = parseFloat($(this).val()) || 0;
        const dueAfterPayment = grandTotal - payment;
        $('#due_after_payment').val(isNaN(dueAfterPayment) ? '0.00' : dueAfterPayment.toFixed(2));
    });
});
</script>
@endpush

