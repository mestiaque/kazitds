@extends('me::master')

@section('title', trans('kazitds::kazitds.Edit Sale'))

@push('buttons')
  @component('me::components.btn.add-button', [
      'route' => route('sales.index'),
      'text' => __('kazitds::kazitds.Sales List'),
      'class' => 'btn-encodex-list'
  ])
  @endcomponent
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
