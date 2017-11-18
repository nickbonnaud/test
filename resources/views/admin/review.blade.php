@extends('layouts.layoutDashboard')
@section('content')
<div class="content-wrapper-scroll">
  <div class="scroll-main">
    <div class="scroll-main-contents">
    	<section class="content-header">
        <h1>
          Businesses Pending Review
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Business Review</li>
        </ol>
      </section>
    	<section class="content">
    		<div class="scroll-container-analytics">
    			<div class="scroll-contents">
		    		<div class="row">
			      	<div class="col-xs-12">
			      		<div class="box box-primary">
			      			<div class="box-header with-border">
			      				<h3 class="box-title-inventory">Pending Profile Approval</h3>
			      			</div>
			      			<div class="box-body table-responsive no-padding">
			      				<table class="table table-striped">
			      					<tbody>
			      						<tr>
			      							<th>Name</th>
			      							<th>Website</th>
			      							<th>Description</th>
			      							<th>Hero</th>
			      							<th>Logo</th>
			      							<th>Approve</th>
			      						</tr>
			      						@include('partials.admin.profiles_pending')
			      					</tbody>
			      				</table>
			      			</div>
			      		</div>
			      	</div>
			      </div>
			      <div class="row">
			      	<div class="col-xs-12">
			      		<div class="box box-primary">
			      			<div class="box-header with-border">
			      				<h3 class="box-title-inventory">Pending Account Approval</h3>
			      			</div>
			      			<div class="box-body table-responsive no-padding">
			      				<table class="table table-striped">
			      					<tbody>
			      						<tr>
			      							<th>Name</th>
			      							<th>Business Info</th>
			      							<th>Owner Info</th>
			      							<th>Bank Info</th>
			      							<th>Description</th>
			      							<th>Approve</th>
			      						</tr>
			      						@include('partials.admin.accounts_pending')
			      					</tbody>
			      				</table>
			      			</div>
			      		</div>
			      	</div>
			      </div>
			  	</div>
			  </div>
    	</section>
    </div>
  </div>
</div>
@stop