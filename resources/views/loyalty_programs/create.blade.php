@extends('layouts.layoutDashboard')
@section('content')
<div class="content-wrapper-scroll">
	<div class="scroll-main">
		<div class="scroll-main-contents">
			<section class="content-header">
		    <h1>
		      Create your Customer Loyalty Program
		    </h1>
		    <ol class="breadcrumb">
		      <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
		      <li class="active">Loyalty Program</li>
		    </ol>
		  </section>
		  @include ('errors.form')
			<section class="content">
				<create-loyalty inline-template>
					<div class="col-md-12">
						<div class="box box-primary">
							<div class="box-header with-border">
								<h3 class="box-title">Program Details</h3>
							</div>
							@include('partials.loyalty_programs.form_create')
						</div>
					</div>
				</create-loyalty>
			</section>
		</div>
	</div>
</div>
@stop
