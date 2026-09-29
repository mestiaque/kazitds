@extends('me::master')

@section('title', __('kazitds::kazitds.Sale Returns'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">@lang('kazitds::kazitds.Sale Returns')</h3>
                    <a href="{{ route('sale-returns.create') }}" class="btn btn-encodex">
                        <i class="fas fa-plus"></i> @lang('kazitds::kazitds.New Sale Return')
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('kazitds::kazitds.ID')</th>
                                    <th>@lang('kazitds::kazitds.Sale ID')</th>
                                    <th>Customer</th>
                                    <th>@lang('kazitds::kazitds.Return Date')</th>
                                    <th>@lang('kazitds::kazitds.Total Amount')</th>
                                    <th>@lang('kazitds::kazitds.Status')</th>
                                    <th>@lang('kazitds::kazitds.Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($saleReturns as $return)
                                <tr>
                                    <td>{{ $return->id }}</td>
                                    <td>
                                        <a href="{{ route('sales.show', $return->sale_id) }}">
                                            #{{ $return->sale_id }}
                                        </a>
                                    </td>
                                    <td>{{ $return->sale->customer->name ?? '--' }}</td>
                                    <td>{{ $return->return_date->format('d M Y') }}</td>
                                    <td>${{ toBanglaNumber($return->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $return->status === 'approved' ? 'success' : ($return->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($return->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('sale-returns.show', $return) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($return->status === 'pending')
                                            <a href="{{ route('sale-returns.edit', $return) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('sale-returns.destroy', $return) }}" method="POST" style="display: inline;">
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
                                    <td colspan="7" class="text-center">@lang('kazitds::kazitds.No sale returns found.')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $saleReturns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
