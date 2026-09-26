@extends('me::master')

@section('title', trans('kazitds::kazitds.Edit Purchase'))

@section('content')
<div class="card shadow mb-4 w-100">
    <div class="card-body">
        <form action="{{ route('purchases.update', $purchase->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('kazitds::purchases._form')
        </form>
    </div>
</div>
@endsection
