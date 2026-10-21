@extends('me::master')

@section('title', trans('kazitds::kazitds.Edit Sale'))

@push('buttons')
  <a href="{{ route('sales.index') }}" class="btn btn-sm btn-encodex-list">
      <i class="fas fa-list"></i> @lang('kazitds::kazitds.Sales List')
  </a>
@endpush

@push('css')
<style>
    /* Remove arrows from number inputs */
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <form action="{{ route('sales.update', $sale->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('kazitds::sales._form')
    </form>
</div>
@endsection
