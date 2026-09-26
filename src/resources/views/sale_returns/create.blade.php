@extends('me::master')

@section('title', 'Create Sale Return')

@push('css')
<style>
    .sale-items-table {
        margin-top: 20px;
    }
    .hidden {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Sale Return</h3>
                    <div class="card-tools">
                        <a href="{{ route('sale-returns.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form action="{{ route('sale-returns.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sale_id">Sale *</label>
                                    <select class="form-control @error('sale_id') is-invalid @enderror" id="sale_id" name="sale_id" required>
                                        <option value="">Select Sale</option>
                                        @foreach($sales as $sale)
                                        <option value="{{ $sale->id }}" {{ old('sale_id') == $sale->id ? 'selected' : '' }}>
                                            #{{ $sale->invoice_number }} - {{ $sale->customer->name ?? '--' }} ({{ $sale->sale_date->format('d M Y') }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('sale_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="return_date">Return Date *</label>
                                    <input type="date" class="form-control @error('return_date') is-invalid @enderror"
                                           id="return_date" name="return_date" value="{{ old('return_date', date('Y-m-d')) }}" required>
                                    @error('return_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_amount">Total Return Amount *</label>
                                    <input type="number" class="form-control @error('total_amount') is-invalid @enderror"
                                           id="total_amount" name="total_amount" value="{{ old('total_amount', 0) }}"
                                           step="0.01" min="0" required>
                                    <small class="text-info">
                                        <i class="fas fa-info-circle"></i>
                                        This amount will be automatically calculated when you select a sale.
                                    </small>
                                    @error('total_amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ old('status', 'approved') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reason">Reason for Return</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror"
                                      id="reason" name="reason" rows="3" placeholder="Enter reason for return">{{ old('reason') }}</textarea>
                            @error('reason')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3" placeholder="Additional notes">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Sale Items Table -->
                        <div id="sale-items-container" class="hidden">
                            <hr>
                            <h4>Sale Items to Return</h4>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> All available items from this sale will be automatically returned.
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Original Qty</th>
                                            <th>Already Returned</th>
                                            <th>Qty to Return</th>
                                            <th>Price Per Unit</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sale-items-body">
                                        <!-- Items will be populated via JavaScript -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-end">Total Return Amount:</th>
                                            <th id="total-return-amount">0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-encodex">
                            <i class="fas fa-save"></i> Create Sale Return
                        </button>
                        <a href="{{ route('sale-returns.index') }}" class="btn btn-encodex-cancel">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Make sure jQuery is loaded
    console.log("jQuery version:", $.fn.jquery);

    $(document).ready(function() {
        console.log("DOM ready, initializing sale return form");
        // When sale is selected, fetch its items
        $('#sale_id').change(function() {
            const saleId = $(this).val();
            console.log("Sale selected:", saleId);

            if (!saleId) {
                $('#sale-items-container').addClass('hidden');
                return;
            }

            // Use a different approach for building the URL
            // Convert the route template to a full URL with the saleId
            const baseUrl = "{{ url('/sale-returns/get-sale-items') }}";
            const ajaxUrl = baseUrl + "/" + saleId;
            console.log("AJAX URL:", ajaxUrl);

            // Fetch sale items via AJAX - using the correct route URL
            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    // Show loading indicator
                    $('#sale-items-body').html('<tr><td colspan="6" class="text-center">Loading items...</td></tr>');
                    $('#sale-items-container').removeClass('hidden');
                },
                success: function(response) {
                    let itemsHtml = '';
                    let totalAmount = 0;

                    if (response.items.length === 0) {
                        itemsHtml = '<tr><td colspan="6" class="text-center">No items available for return</td></tr>';
                    } else {
                        response.items.forEach(item => {
                            if (item.remaining <= 0) {
                                // Skip already fully returned items
                                return;
                            }

                            // Ensure values are treated as numbers for calculation
                            const remaining = parseFloat(item.remaining);
                            const pricePerUnit = parseFloat(item.price_per_unit);
                            const subtotal = remaining * pricePerUnit;

                            // Add to total (ensure it's a number operation)
                            totalAmount = totalAmount + subtotal;

                            itemsHtml += `
                                <tr>
                                    <td>${item.product_name}</td>
                                    <td>${item.quantity}</td>
                                    <td>${item.already_returned}</td>
                                    <td>${remaining}</td>
                                    <td>${pricePerUnit.toFixed(2)}</td>
                                    <td>${subtotal.toFixed(2)}</td>
                                </tr>
                            `;
                        });
                    }

                    // Debug: Log the calculated amount to console
                    console.log("Calculated total amount:", totalAmount);

                    $('#sale-items-body').html(itemsHtml);
                    $('#total-return-amount').text(totalAmount.toFixed(2));

                    // Ensure the value is set correctly and trigger change event
                    $('#total_amount').val(totalAmount.toFixed(2));
                    $('#total_amount').prop('readonly', true);
                    $('#total_amount').trigger('change');
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    $('#sale-items-body').html('<tr><td colspan="6" class="text-center text-danger">Error loading sale items. Please check console for details.</td></tr>');
                }
            });
        });
    });
</script>
@endpush
