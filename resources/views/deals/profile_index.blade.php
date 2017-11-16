@extends('layouts.layoutDashboard')
@section('content')
<deals inline-template v-cloak>
	<div class="content-wrapper-scroll">
		<div class="scroll-main">
			<div class="scroll-main-contents">
				<section class="content-header">
			    <h1>
			      Add | Active Deals
			    </h1>
			    <ol class="breadcrumb">
			      <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
			      <li class="active">Deals</li>
			    </ol>
			  </section>
				<section class="content">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="box box-primary">
							<div class="box-header with-border">
								<h3 class="box-title">Create a New Deal</h3>
								<a class="pull-right" href="#" data-toggle="modal" data-target="#dealInfoModal"><p>Info About Deals</p></a>
							</div>
								@include ('errors.form')
								@include('partials.deals.form_create')
						</div>
					</div>
					<div class="scroll-container-deals col-md-6 col-sm-6 col-xs-12">
						<div class="scroll-contents">
							@include('partials.deals.index', ['posts' => $deals, 'no_icons' => true])
						</div>
					</div>
				</section>
			</div>
		</div>


		<div class="modal fade" id="dealInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header-timeline">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="dealInfoModal">Details about using Pockeyt Deals</h4>
		      </div>
		      <div class="modal-body-customer-info">
		      	<div class="box-body">
							<h4><strong>Pockeyt does not provide or pay for these Deals</strong></h4>
							<p>Pockeyt's Deal system is meant for tracking purchases that your Customers make in the Pockeyt app.</p>
							<p>These Deals are visible in the Pockeyt app as a regular post, with the exception of a "Get now!" button.</p>
							<p>Pockeyt's Deal system is meant for you, the Business, to more easily engage your Customers by allowing them to purchase items or services directly in the Pockeyt App and redeem these purchases when they visit your establishment.</p>
							<p>Pockeyt Deals <strong>do not have to be monetary discounts.</strong> For example, Pockeyt encourages you, the Business, to use the Deals system to bring attention to new products, pay entrance to an event ahead of time, or allow customers access to an early release product.</p>
							<p>Pockeyt's Deal system allows customers to redeem purchased Deals when they are physically in your establishment. Customers who have bought a Deal will have a Redeem Deal button under their profile when visible on your Customer Dashboard.</p>
							<p>It is the Businesses' responsibility to provide the customer with Deal specified when the Business created the Deal. Pockeyt <strong>does not provide</strong> Deals to your customers.</p>
						</div>
		      </div>
		      <div class="modal-footer">
					  <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					</div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="dealModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header-timeline">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="dealModal">Fresh stats just for you!</h4>
		      </div>
		      <div class="modal-body-deals">
		      	<div class="box-body">
		      		<div class="col-md-6 col-sm-12 col-xs-12">
								<div class="info-box">
									<span class="info-box-icon bg-green">
										<i class="fa fa-smile-o"></i>
									</span>
									<div class="info-box-content">
										<span class="info-box-text">Redeemed</span>
										<span class="info-box-number">@{{ redeemed }}</span>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<div class="info-box">
									<span class="info-box-icon bg-aqua">
										<i class="fa fa-bolt"></i>
									</span>
									<div class="info-box-content">
										<span class="info-box-text">Purchased</span>
										<span class="info-box-number">@{{ purchasedDeals.length }}</span>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12 col-xs-12">
								<div class="info-box">
									<span class="info-box-icon bg-yellow">
										<i class="fa fa-hourglass-o"></i>
									</span>
									<div class="info-box-content">
										<span class="info-box-text">Outstanding</span>
										<span class="info-box-number">@{{ outstanding }}</span>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12 col-xs-12">
								<div class="info-box">
									<span class="info-box-icon bg-green">
										<i class="fa fa-dollar"></i>
									</span>
									<div class="info-box-content">
										<span class="info-box-text">Earned</span>
										<span class="info-box-number">$@{{ total }}</span>
									</div>
								</div>
							</div>
		      	</div>
		    	</div>
		  	</div>
			</div>
		</div>
	</div>
</deals>
@stop

@section('scripts.footer')
	<script>

		$(function() {
      $( "#end_date_pretty" ).datepicker({
          dateFormat: "DD, d MM, yy",
          altField: "#end_date",
          altFormat: "yy-mm-dd"
      });
    });

	</script>
@stop

