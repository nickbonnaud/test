@extends('layouts.layoutDashboard')
@section('content')
<?php
$qbo_obj = new \App\Http\Controllers\QuickbooksController();
$qbo_connect = $qbo_obj->qboConnect();
?>
<connections inline-template v-cloak>
	<div class="content-wrapper-scroll">
		<div class="scroll-main">
			<div class="scroll-main-contents">
				<section class="content-header">
			    <h1>
			      Your Account Connections
			    </h1>
			    <ol class="breadcrumb">
			      <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
			      <li class="active">Account Connections Info</li>
			    </ol>
			  </section>
			  @include ('errors.form')
				<section class="content">
					<div class="col-md-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<h3 class="box-title">Connections and Status</h3>
							</div>
							<connection-table v-bind:profile-initial="{{ $profile->withAccountAndTax() }}"></connection-table>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
</connections>
@stop
@section ('scripts.footer')
<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
<script>
	intuit.ipp.anywhere.setup({
    menuProxy: '{{ env("QBO_MENU_URL") }}',
    grantUrl: '{{ env("QBO_OAUTH_URL") }}'
  });
</script>

