@extends('layouts.layoutDashboard')
@section('content')
<account v-bind:account="{{ $account }}" inline-template v-cloak>
	<div class="content-wrapper-scroll">
		<div class="scroll-main">
			<div class="scroll-main-contents">
				<section class="content-header">
			    <h1>
			      Your Business Account Profile
			    </h1>
			    @if($account->status == 'pending' || $account->status == 'review')
			    	<p><i class="fa fa-circle text-warning"></i> Payment Account Pending</p>
			    @elseif($account->status == 'Boarded')
			    	<p><i class="fa fa-circle text-success"></i> Payment Account Active</p>
			    @else
			    	<p><i class="fa fa-circle text-danger"></i> Payment Account Not Approved</p>
			    	<p>{{ $account->status }}</p>
			    @endif
			    <ol class="breadcrumb">
			      <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
			      <li class="active">Payment Account Info</li>
			    </ol>
			  </section>
			  @include ('errors.form')
				<section class="content">
					<div class="scroll-container-analytics">
						<div class="scroll-contents">
							<div class="col-md-6">
								<div class="box box-primary">
									<div class="box-header with-border">
										<h3 class="box-title">Individual Account Holder Info</h3>
									</div>
									<div class="box-body">
										<ul class="list-group list-group-unbordered">
											<li class="list-group-item">
												<b>First Name</b>
												<p class="pull-right">{{ $account->accountUserFirst }}</p>
											</li>
											<li class="list-group-item">
												<b>Last Name</b>
												<p class="pull-right">{{ $account->accountUserLast }}</p>
											</li>
											<li class="list-group-item">
												<b>Email</b>
												<p class="pull-right">{{ $account->ownerEmail }}</p>
											</li>
											<li class="list-group-item">
												<b>Date of Birth</b>
												<p class="pull-right">{{ $account->dateOfBirth }}</p>
											</li>
											<li class="list-group-item">
												<b>SSN last 4</b>
												<p class="pull-right">{{ $account->ssn }}</p>
											</li>
											<li class="list-group-item">
												<b>Percentage Ownership</b>
												<p class="pull-right">{{ $account->ownership }}%</p>
											</li>
											<li class="list-group-item">
												<b>Street Address</b>
												<p class="pull-right">{{ $account->indivStreetAddress }}</p>
											</li>
											<li class="list-group-item">
												<b>City</b>
												<p class="pull-right">{{ $account->indivCity }} </p>
											</li>
											<li class="list-group-item">
												<b>State</b>
												<p class="pull-right">{{ $account->indivState }}</p>
											</li>
											<li class="list-group-item">
												<b>Zip</b>
												<p class="pull-right">{{ $account->indivZip }}</p>
											</li>
										</ul>
										<a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#individualAccountInfoModal">
					          	<b>Change</b>
					        	</a>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="box box-primary">
									<div class="box-header with-border">
										<h3 class="box-title">Business Info</h3>
									</div>
									<div class="box-body">
										<ul class="list-group list-group-unbordered">
											<li class="list-group-item">
												<b>Legal Business Name</b>
												<p class="pull-right">{{ $account->legalBizName }}</p>
											</li>
											<li class="list-group-item">
												<b>Type</b>
												<p class="pull-right">{{ $account->businessTypeName() }}</p>
											</li>
											<li class="list-group-item">
												<b>Tax ID (EIN)</b>
												<p class="pull-right">{{ $account->bizTaxId }}</p>
											</li>
											<li class="list-group-item">
												<b>Date Established</b>
												<p class="pull-right">{{ $account->established }}</p>
											</li>
											<li class="list-group-item">
												<b>Estimated Annual Credit Card Sales</b>
												<p class="pull-right">${{ $account->annualCCSales }}</p>
											</li>
											<li class="list-group-item">
												<b>Street Address</b>
												<p class="pull-right">{{ $account->bizStreetAddress }}</p>
											</li>
											<li class="list-group-item">
												<b>City</b>
												<p class="pull-right">{{ $account->bizCity }} </p>
											</li>
											<li class="list-group-item">
												<b>State</b>
												<p class="pull-right">{{ $account->bizState }}</p>
											</li>
											<li class="list-group-item">
												<b>Zip</b>
												<p class="pull-right">{{ $account->bizZip }}</p>
											</li>
											<li class="list-group-item">
												<b>Business Phone</b>
												<p class="pull-right">{{ $account->phone }}</p>
											</li>
											<li class="list-group-item">
												<b>Business Email</b>
												<p class="pull-right">{{ $account->accountEmail }}</p>
											</li>
										</ul>
										<a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#businessAccountInfoModal">
					          	<b>Change</b>
					        	</a>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="box box-primary last-box">
									<div class="box-header with-border">
										<h3 class="box-title">Business Account Info</h3>
									</div>
									<div class="box-body">
										<ul class="list-group list-group-unbordered">
											<li class="list-group-item">
												<b>Account Type</b>
												<p class="pull-right">{{ $account->methodName() }}</p>
											</li>
											<li class="list-group-item">
												<b>Account Number last 4</b>
												<p class="pull-right">{{ $account->accountNumber }}</p>
											</li>
											<li class="list-group-item">
												<b>Routing Number last 4</b>
												<p class="pull-right">{{ $account->routing }}</p>
											</li>
										</ul>
										<a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#sensitiveAccountInfoModal">
					          	<b>Change</b>
					        	</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
		<div class="modal fade" id="individualAccountInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header-timeline">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="individualAccountInfoModal">Edit Your Personal Info</h4>
		      </div>
		      <div class="modal-body-customer-info">
		        @include ('partials.accounts.form_edit_owner')
		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="businessAccountInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header-timeline">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="businessAccountInfoModal">Edit Business Info</h4>
		      </div>
		      <div class="modal-body-customer-info">
		        @include ('partials.accounts.form_edit_business')
		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="sensitiveAccountInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header-timeline">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="sensitiveAccountInfoModal">Edit Business Info</h4>
		      </div>
		      <div class="modal-body-customer-info">
		        @include ('partials.accounts.form_edit_bank')
		      </div>
		    </div>
		  </div>
		</div>
	</div>
</account>
@stop