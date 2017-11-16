@extends('layouts.layoutDashboard')
@section('content')
<team v-bind:team-members="{{ $employees }}" profile-slug="{{ $profile->slug }}" inline-template v-cloak>
	<div>
		<div class="content-wrapper-scroll">
			<div class="scroll-main">
				<div class="scroll-main-contents">
					<section class="content-header">
				    <h1>
				      Team
				    </h1>
				    <a v-if="unlock != true" class="pull-right" v-if="employeesOn.length > 0 || employeesOff.length > 0" href="#" style="display: inline-block;"  v-on:click="unlock = true">
			    		<button class="btn btn-danger quick-button">Remove Team Member</button>
			    	</a>
			    	<a v-if="unlock == true" class="pull-right" href="#" style="display: inline-block;" v-on:click="unlock = false">
			    		<button class="btn btn-success quick-button">Finish</button>
			    	</a>
			    	<a href="#" data-toggle="modal" data-target="#addEmployeeModal" style="display: inline-block;">
			    		<button class="btn pull-left btn-primary quick-button">New Team Member</button>
			    	</a>
				    <ol class="breadcrumb">
				      <li><a href="{{ route('profiles.show', ['profiles' => Crypt::encrypt($user->profile->id)]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
				      <li class="active">Team</li>
				    </ol>
				  </section>
					<section class="content">
						<div class="scroll-container-analytics">
							<div class="scroll-contents">
								<shift-tracker :unlock="unlock" :employees-on="employeesOn" :employees-off="employeesOff" profile-slug="{{ $profile->slug }}"></shift-tracker>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
		<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header-timeline">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="addProductModal">Add Team Member*</h4>
		        <p style="margin: 0px; color: #777777; font-size: 12px;">For security purposes, new Team Members must first have an account on the Pockeyt mobile app.</p>
		      </div>
		      <search-users :profile="{{ $profile }}" id="addEmployeeModal"></search-users>
		    </div>
		  </div>
		</div>
	</div>
</team>
@stop