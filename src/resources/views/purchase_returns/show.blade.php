@extends('me::master')

@section('title', 'Purchase Return Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Return #{{ $purchaseReturn->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('purchase-returns.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        @if($purchaseReturn->status === 'pending')
                        <a href="{{ route('purchase-returns.edit', $purchaseReturn) }}" class="btn btn-warning">
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
                                    <td>#{{ $purchaseReturn->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Return Date:</strong></td>
                                    <td>{{ $purchaseReturn->return_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $purchaseReturn->status === 'approved' ? 'success' : ($purchaseReturn->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($purchaseReturn->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td>${{ toBanglaNumber($purchaseReturn->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created At:</strong></td>
                                    <td>{{ $purchaseReturn->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                @if($purchaseReturn->updated_at != $purchaseReturn->created_at)
                                <tr>
                                    <td><strong>Updated At:</strong></td>
                                    <td>{{ $purchaseReturn->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Purchase Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Purchase ID:</strong></td>
                                    <td>
                                        <a href="{{ route('purchases.show', $purchaseReturn->purchase_id) }}">
                                            #{{ $purchaseReturn->purchase_id }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Supplier:</strong></td>
                                    <td>{{ $purchaseReturn->purchase->supplier->name ?? __('kazitds::kazitds.N/A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Purchase Date:</strong></td>
                                    <td>{{ $purchaseReturn->purchase->purchase_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Purchase Total:</strong></td>
                                    <td>${{ toBanglaNumber($purchaseReturn->purchase->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($purchaseReturn->reason)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Reason for Return</h5>
                            <div class="alert alert-info">
                                {{ $purchaseReturn->reason }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($purchaseReturn->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Notes</h5>
                            <div class="alert alert-secondary">
                                {{ $purchaseReturn->notes }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    @if($purchaseReturn->status === 'pending')
                    <div class="btn-group" role="group">
                        <form action="{{ route('purchase-returns.update', $purchaseReturn) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this return?')">
                                <i class="fas fa-check"></i> Approve Return
                            </button>
                        </form>
                        <form action="{{ route('purchase-returns.update', $purchaseReturn) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
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
