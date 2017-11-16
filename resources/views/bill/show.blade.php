@extends('layouts.layoutDashboard')
@section('content')
<bill inline-template v-cloak>
  <div class="content-wrapper-scroll" >
    <div class="scroll-main">
      <div class="scroll-main-contents">
        <section class="content-header">
          <h1 class="header-button">
            {{ $customer->first_name }} {{ $customer->last_name }} Bill
          </h1>
          <button data-toggle="modal" data-target="#customItem" type="button" class="btn btn-primary btn-sm custom-amount-btn">Custom Amount</button>
          <ol class="breadcrumb">
            <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Bill</li>
          </ol>
        </section>

        <section class="content">
          <search-default search-type="product"></search-default>
          <div class="scroll-container-analytics col-md-8 col-sm-6 col-xs-6">
            <div class="scroll-contents">
              <inventory-list profile-slug="{{ $profile->slug }}"></inventory-list> 
            </div>
          </div>
          <div class="col-md-4 col-sm-6 col-xs-6">
            <customer-bill :bill="{{ $bill }}" :customer="{{ $customer }}" employee-id="{{ $employeeId }}" :profile="{{ $profile->withTax() }}"></customer-bill>
          </div>
          <custom-bill-item></custom-bill-item>
        </section>
      </div>
    </div>
  </div>
</bill>
@stop