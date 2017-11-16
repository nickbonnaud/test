@extends('layouts.layoutDashboard')
@section('content')
<div id="content">
	<div class="content-wrapper-scroll">
		<div class="scroll-main">
			<div class="scroll-main-contents">
				<section class="content-header">
			    <h1>
			      Your Customer Loyalty Program
			    </h1>
			    <ol class="breadcrumb">
			      <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
			      <li class="active">Loyalty Program</li>
			    </ol>
			  </section>
			  @include ('errors.form')
				<section class="content">
					<div class="col-md-6">
						<div class="box box-primary">
							<div class="box-header with-border">
								<i class="fa fa-trophy"></i>
								<h3 class="box-title">Program Details</h3>
								<div class="pull-right">
									<a href="#" class="btn btn-block btn-danger btn-sm" data-toggle="modal" data-target="#deleteLoyaltyProgram">
                    <b>Delete</b>
                  </a>
								</div>
							</div>
							<div class="box-body">
								@if($loyaltyProgram->is_increment)
									<h4>Your current Loyalty Program requires <b>{{ $loyaltyProgram->purchases_required }}</b> purchases to receive a <b>{{ $loyaltyProgram->reward }}</b>.</h4>
								@else
									<h4>Your current Loyalty Program requires <b>${{ $loyaltyProgram->amount_required }}</b> in total purchases to receive a <b>{{ $loyaltyProgram->reward }}</b>.</h4>
								@endif
								<hr>
								<a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#LoyaltyProgramModal">
			          	<b>Using Pockeyt's reward system</b>
			        	</a>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
	<div class="modal fade" id="LoyaltyProgramModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header-timeline">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="LoyaltyProgramModal">Details about using Pockeyt Rewards</h4>
	      </div>
	      <div class="modal-body-customer-info">
	      	<div class="box-body">
						<h4><strong>Pockeyt does not provide or pay for these rewards</strong></h4>
						<p>Pockeyt's reward system is meant for tracking your customer's progress towards a designated number of purchases or set dollar amount, the Goal, determined by you, the Business.</p>
						<p>Pockeyt's reward system will notify you immediately after a customer's transaction meets or exceeds the Goal.</p>
						<p>It is the Businesses' responsibility to provide the customer with reward specified when the Business created the Reward Program. Pockeyt <strong>does not provide</strong> rewards to your customers.</p>
					</div>
	      </div>
	      <div class="modal-footer">
				  <button type="button" class="btn btn-primary pull-right" data-dismiss="modal">Close</button>
				</div>
	    </div>
	  </div>
	</div>
	<div class="modal fade" id="deleteLoyaltyProgram" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header-timeline">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="deleteLoyaltyProgram">Are you sure?</h4>
	      </div>
	      <div class="modal-body-customer-info">
	      	<div class="box-body">
						<h4><strong>All customer Loyalty Program data will be lost!</strong></h4>
						<p>If you create a new Loyalty Program all your current customers' progress will reset to 0.</p>
					</div>
	      </div>
	      <div class="modal-footer">
				  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				  @include('partials.loyalty_programs.delete')
				</div>
	    </div>
	  </div>
	</div>
</div>
@stop