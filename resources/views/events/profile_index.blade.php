@extends('layouts.layoutDashboard')
@section('content')
<div class="content-wrapper-scroll">
	<div class="scroll-main">
		<div class="scroll-main-contents">
			<section class="content-header">
		    <h1>
		      Add | Recent Events
		    </h1>
		    <ol class="breadcrumb">
		      <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
		      <li class="active">Events</li>
		    </ol>
		  </section>
			<section class="content">
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="box box-primary">
					<div class="box-header with-border">
						<a href="https://graph.facebook.com/v2.8/2243018162591971?access_token=EAACiMDeoXMsBANBXH7vXQtKwt6jnpFCdT21XPuWz0edf8PiEvhR81bN1IZB8ZBoRZCaLIDb7mIIu3efq91AFO29HTmVzWvxlgqpOD9yrSnC0p23UUUgv86MOSggcsuQ6VEvvcScxZBQHkq4tMWdgtbdAOfomVxcqD8tsI8SWGwZDZD">
							<h3 class="box-title">Create a New Event</h3>
						</a>
					</div>
						@include ('errors.form')
						@include('partials.events.form_create')
				</div>
			</div>
				<div class="scroll-container-analytics col-md-6 col-sm-6 col-xs-12">
					<div class="scroll-contents">
						@include('partials.events.index', ['events' => $events, 'no_icons' => true])
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
@stop
@section('scripts.footer')
	<script>
		$(function() {
      $( "#event_date_pretty" ).datepicker({
          dateFormat: "DD, d MM, yy",
          altField: "#event_date",
          altFormat: "yy-mm-dd"
      });
    });
	</script>

@stop