@extends('me::master')

@section('title', trans('kazitds::kazitds.Create New Sale'))

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
<div class="container-fluidx">
    <form action="{{ route('sales.store') }}" method="POST">
        @csrf
        @include('kazitds::sales._form')
    </form>
</div>
@endsection
