@extends('layouts.layoutDashboard')
@section('content')
<sales-history v-bind:sales="{{ $sales }}" v-bind:team-members="{{ $employees }}" profile-slug="{{ $profile->slug }}" inline-template v-cloak>
	<div>
		<div class="content-wrapper-scroll">
			<div class="scroll-main">
				<div class="scroll-main-contents">
					<section class="content-header">
				    <h1>
				      Sales Center
				    </h1>
				    @include('partials.sales_history.form_update_tip_tracking')
				    <h4 style="display: inline-block;" v-show="!customDate">Date Range: Today</h4>
				    <h4 style="display: inline-block;" v-show="customDate">Date Range: </h4>
				    <a style="display: inline-block; font-size: 12px; margin-left: 2px;" v-show="!customDate" href="#" v-on:click="toggleDate()">Change</a>
				    <date-range-picker id="dateRangeSales" v-show="customDate" style="display: inline-block; width: 40%; font-size: 16px;"></date-range-picker>
				    <ol class="breadcrumb">
				      <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
				      <li class="active">Sales Center</li>
				    </ol>
				  </section>
					<section class="content">
						<div class="scroll-container-analytics">
							<div class="scroll-contents">
								<div class="row">
									<sales-history-net :transactions="transactions"></sales-history-net>
									<sales-history-tax :transactions="transactions"></sales-history-tax>
									<sales-history-tip :transactions="transactions"></sales-history-tip>
									<sales-history-total :transactions="transactions"></sales-history-total>
								</div>
								@if($profile->tip_tracking_enabled)
									<employee-tip-tracking :transactions="transactions" :selected-employees="employees"></employee-tip-tracking>
								@endif
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
		<modal id="infoModal">
			<template slot="title">@{{ modalPick }}</template>
			<template slot="body">
				<div class="sub-header">
      		<h3 v-if="fromDate == 'today'">@{{ modalPick }} for Today: <strong>$@{{ modalPickData }}</strong>.</h3>
      		<h3 v-else>@{{ modalPick }} from @{{ fromDate }} to @{{ toDate }}: <strong>$@{{ modalPickData }}</strong>.</h3>
      	</div>
			</template>
		</modal>
	</div>
</sales-history>
@stop