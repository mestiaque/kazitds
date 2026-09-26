@extends('me::master')

@section('title', trans('kazitds::kazitds.Create New Sale'))

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
<div class="container-fluidx">
    <form action="{{ route('sales.store') }}" method="POST">
        @csrf
        @include('kazitds::sales._form')
    </form>
</div>
@endsection
