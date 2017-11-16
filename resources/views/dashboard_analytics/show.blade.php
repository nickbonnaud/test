@extends('layouts.layoutDashboard')
@section('content')
<dashboard-analytics profile-slug="{{ $profile->slug }}" inline-template v-cloak>
	<div class="content-wrapper-scroll">
		<div class="scroll-main">
			<div class="scroll-main-contents">
				<section class="content-header">
			    <h1>
			      Analytics Dashboard
			    </h1>
			    <ol class="breadcrumb">
			      <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
			      <li class="active">Analytics Dashboard</li>
			    </ol>
			  </section>
				<section class="content">
					<div class="scroll-container-analytics">
						<div class="scroll-contents">
							<div class="row">
								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
									<conversion-rate profile-slug="{{ $profile->slug }}" v-bind:total-views="{{ $totalViews }}"></conversion-rate>
								</div>
								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
									<revenue-per-post v-bind:total-views="{{ $totalViews }}" v-bind:total-revenue="{{ $totalRevenue }}"></revenue-per-post>
								</div>
								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
									<top-day :interactions-day="interactionsByDay"></top-day>
								</div>
								<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
									<top-hour :interactions-hour="interactionsByHour"></top-hour>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<top-posts-interactions profile-slug="{{ $profile->slug }}"></top-posts-interactions>
									<activity-day profile-slug="{{ $profile->slug }}" v-bind:total-views="{{ $totalViews }}" v-bind:total-revenue="{{ $totalRevenue }}" :interactions-day="interactionsByDay"></activity-day>
								</div>
								<div class="col-md-6">
									<top-posts-revenue profile-slug="{{ $profile->slug }}"></top-posts-revenue>
									<activity-hour profile-slug="{{ $profile->slug }}" v-bind:total-views="{{ $totalViews }}" v-bind:total-revenue="{{ $totalRevenue }}" :interactions-hour="interactionsByHour"></activity-hour>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
		<div class="modal fade" id="showPost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header-timeline">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="showPostModal">Post Details | Interaction Type Breakdown</h4>
		      </div>
		      <div class="modal-body-analytics">
	        	<div class="col-md-6 col-sm-6 col-xs-12">
	        		<div class="box box-primary modal-analytics">
	        			<div class="box-header with-border">
	                <h4 v-if="selectedPost.message" class="box-title">@{{ selectedPost.message | truncate }}</h4>
	                <h4 v-else="!selectedPost.message" class="box-title">@{{ selectedPost.title }}</h4>
	        			</div>
	        			<div class="box-body">
	        				<div v-if="selectedPost.thumb_path" class="analytics-modal-image">
	        					<img :src="selectedPost.thumb_path">
	        					<hr>
	        				</div>
	            		<p class="analytics-date">Posted on <strong>@{{ selectedPost.published_at | setDate }}</strong>.</p>
	        			</div>
	        		</div>
	         	</div>
	         	<div class="col-md-6 col-sm-6 col-xs-12">
	         		<div class="box box-primary modal-analytics">
								<div class="box-header with-border">
									<h3 class="box-title">Interaction Breakdown</h3>
								</div>
								<interaction-breakdown :selected-post="selectedPost"></interaction-breakdown>
		         	</div>
	         	</div>
		      </div>
		    </div>
		  </div>
		</div>
	</div>
</dashboard-analytics>
@stop