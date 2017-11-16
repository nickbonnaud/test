@extends('layouts.layoutBasic')
@section('content')
<div class="row" id="owner">
  <div class="col-md-12">
    <h1>Payment Account Setup</h1>
    <p class="pull-right">Part 2 of 3</p>
    <h4>Business Owner Info</h4>
    <hr>
    @include ('errors.form')
  	@include ('partials.accounts.form_create_owner')
  </div>
</div>
@stop
@section('scripts.footer')
<script>
    
  Vue.use(VueMask.VueMaskPlugin);
  var owner = new Vue({
    el: '#owner',

    data: {
      ownership: '{{ old('ownership') }}',
      indivState: '{{ old('indivState') }}',
      indivZip: '{{ old('indivZip') }}',
      ssn: '{{ old('ssn') }}'
    },
  });

</script>
@stop