@extends('me::master')

@section('title', 'Sale Return Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sale Return #{{ $saleReturn->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('sale-returns.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        @if($saleReturn->status === 'pending')
                        <a href="{{ route('sale-returns.edit', $saleReturn) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Return Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Return ID:</strong></td>
                                    <td>#{{ $saleReturn->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Return Date:</strong></td>
                                    <td>{{ $saleReturn->return_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $saleReturn->status === 'approved' ? 'success' : ($saleReturn->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($saleReturn->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td>${{ toBanglaNumber($saleReturn->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created At:</strong></td>
                                    <td>{{ $saleReturn->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                @if($saleReturn->updated_at != $saleReturn->created_at)
                                <tr>
                                    <td><strong>Updated At:</strong></td>
                                    <td>{{ $saleReturn->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Sale Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Sale ID:</strong></td>
                                    <td>
                                        <a href="{{ route('sales.show', $saleReturn->sale_id) }}">
                                            #{{ $saleReturn->sale_id }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Customer:</strong></td>
                                    <td>{{ $saleReturn->sale->customer->name ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sale Date:</strong></td>
                                    <td>{{ $saleReturn->sale->sale_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sale Total:</strong></td>
                                    <td>${{ toBanglaNumber($saleReturn->sale->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($saleReturn->reason)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Reason for Return</h5>
                            <div class="alert alert-info">
                                {{ $saleReturn->reason }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($saleReturn->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Notes</h5>
                            <div class="alert alert-secondary">
                                {{ $saleReturn->notes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Returned Items</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price Per Unit</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($relatedReturns) && $relatedReturns->count() > 0)
                                            @foreach($relatedReturns as $item)
                                                <tr>
                                                    <td>{{ $item->productVariant->product->name ?? 'Unknown Product' }}</td>
                                                    <td>{{ $item->returned_quantity }}</td>
                                                    <td>${{ toBanglaNumber($item->return_price_per_unit, 2) }}</td>
                                                    <td>${{ toBanglaNumber($item->total_return_amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td>{{ $saleReturn->productVariant->product->name ?? 'Unknown Product' }}</td>
                                                <td>{{ $saleReturn->returned_quantity }}</td>
                                                <td>${{ toBanglaNumber($saleReturn->return_price_per_unit, 2) }}</td>
                                                <td>${{ toBanglaNumber($saleReturn->total_return_amount, 2) }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total:</th>
                                            <th>${{ toBanglaNumber($saleReturn->total_amount, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    @if($saleReturn->status === 'pending')
                    <div class="btn-group" role="group">
                        <form action="{{ route('sale-returns.update', $saleReturn->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="sale_id" value="{{ $saleReturn->sale_id }}">
                            <input type="hidden" name="return_date" value="{{ $saleReturn->return_date->format('Y-m-d') }}">
                            <input type="hidden" name="total_amount" value="{{ $saleReturn->total_amount }}">
                            <input type="hidden" name="reason" value="{{ $saleReturn->reason }}">
                            <input type="hidden" name="notes" value="{{ $saleReturn->notes }}">
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this return?')">
                                <i class="fas fa-check"></i> Approve Return
                            </button>
                        </form>
                        <form action="{{ route('sale-returns.update', $saleReturn) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="sale_id" value="{{ $saleReturn->sale_id }}">
                            <input type="hidden" name="return_date" value="{{ $saleReturn->return_date->format('Y-m-d') }}">
                            <input type="hidden" name="total_amount" value="{{ $saleReturn->total_amount }}">
                            <input type="hidden" name="reason" value="{{ $saleReturn->reason }}">
                            <input type="hidden" name="notes" value="{{ $saleReturn->notes }}">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this return?')">
                                <i class="fas fa-times"></i> Reject Return
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
