@extends('layouts.layoutBasic')
@section('content')
<div class="row" id="payment">
  <div class="col-md-12">
    <h1>Payment Account Setup</h1>
    <p class="pull-right">Part 1 of 3</p>
    <h4>Business Info</h4>
    <hr>
    @include ('errors.form')
    @include ('partials.accounts.form_create_business')
  </div>
</div>
@stop
@section('scripts.footer')
<script>
  Vue.use(VueMask.VueMaskPlugin);
  var payment = new Vue({
    el: '#payment',

    components: {
      VMoney
    },

    data: {
      bizTaxId: '{{ old('bizTaxId') }}',
      annualCCSales: '',
      bizState: '{{ old('bizState') }}',
      bizZip: '{{ old('bizZip') }}',
      phone: '{{ old('phone') }}',
      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: true
      }
    },
  });

</script>
@stop