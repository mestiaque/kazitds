@extends('me::master')

@section('title', __('kazitds::kazitds.Purchase Returns'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">@lang('kazitds::kazitds.Purchase Returns')</h3>
                    <a href="{{ route('purchase-returns.create') }}" class="btn btn-encodex">
                        <i class="fas fa-plus"></i> @lang('kazitds::kazitds.New Purchase Return')
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('kazitds::kazitds.ID')</th>
                                    <th>@lang('kazitds::kazitds.Purchase ID')</th>
                                    <th>Supplier</th>
                                    <th>@lang('kazitds::kazitds.Return Date')</th>
                                    <th>@lang('kazitds::kazitds.Total Amount')</th>
                                    <th>Status</th>
                                    <th>@lang('kazitds::kazitds.Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseReturns as $return)
                                <tr>
                                    <td>{{ $return->id }}</td>
                                    <td>
                                        <a href="{{ route('purchases.show', $return->purchase_id) }}">
                                            #{{ $return->purchase_id }}
                                        </a>
                                    </td>
                                    <td>{{ $return->purchase->supplier->name ?? __('kazitds::kazitds.N/A') }}</td>
                                    <td>{{ $return->return_date->format('d M Y') }}</td>
                                    <td>${{ toBanglaNumber($return->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $return->status === 'approved' ? 'success' : ($return->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($return->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('purchase-returns.show', $return) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($return->status === 'pending')
                                            <a href="{{ route('purchase-returns.edit', $return) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('purchase-returns.destroy', $return) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick='return confirm(@json(__('kazitds::kazitds.Are you sure?')))' >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">@lang('kazitds::kazitds.No purchase returns found.')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $purchaseReturns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
