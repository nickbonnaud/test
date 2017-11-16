@extends('layouts.layoutBasic')
@section('content')
<div class="row" id="bank">
  <div class="col-md-12">
    <h1>Payment Account Setup</h1>
    <p class="pull-right">Part 3 of 3</p>
    <h4>Banking Info</h4>
    <hr>
    @include ('errors.form')
    @include ('partials.accounts.form_create_bank')
  </div>
</div>
<div class="modal fade" id="ToSModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="ToSModal">Account Terms of Service and Privacy Policy</h4>
      </div>
      <div class="modal-body">
        <p>1. Please review Pockeyt's Privacy Policy, found <a href="{{ route('policy.privacy') }}" target="_blank">here</a></p>
        <p>2. Please review Pockeyt's End-User License Agreement, found <a href="{{ route('policy.end-user') }}" target="_blank">here</a></p>
      </div>
    </div>
  </div>
</div>
@stop
@section('scripts.footer')
<script>
    
  Vue.use(VueMask.VueMaskPlugin);
  var bank = new Vue({
    el: '#bank',

    data: {
      routing: '{{ old('routing') }}'
    },
  });
</script>
@stop