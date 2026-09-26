@extends('me::master')

@section('title', trans('kazitds::kazitds.Add Purchase'))

@section('content')
<div class="card shadow mb-4 w-100">
    <div class="card-body">
        <form action="{{ route('purchases.store') }}" method="POST">
            @csrf
            @include('kazitds::purchases._form')
        </form>
    </div>
</div>
@endsection
