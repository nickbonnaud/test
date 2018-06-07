@extends('layouts.layoutDashboard')
@section('content')
<dashboard-main profile-slug="{{ $profile->slug }}" inline-template v-cloak>
  <div class="content-wrapper-scroll" >
    <div class="scroll-main">
      <div class="scroll-main-contents">
        <section class="content-header">
          <h1>
            Customer Dashboard
          </h1>
          <ol class="breadcrumb">
            <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
          </ol>
        </section>
        <section class="content">
          <search-default search-type="customer"></search-default>
          <a href="{{ action('\App\Http\Controllers\Api\Mobile\DealsController@test') }}"><button>Do it</button></a>
          <div class="scroll-container">
            <customer-list :profile="{{ $profile }}"></customer-list>
          </div>
        </section>
      </div>
    </div>
    <customer-info-modal></customer-info-modal>
    <employee-select-modal profile-slug="{{ $profile->slug }}"></employee-select-modal>
    <deal-modal profile-slug="{{ $profile->slug }}"></deal-modal>
    <reward-modal profile-slug="{{ $profile->slug }}"></reward-modal>
  </div>
</dashboard-main>
@stop