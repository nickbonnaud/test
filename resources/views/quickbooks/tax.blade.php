@extends('layouts.layoutPost')
@section('content')
<div class="content-wrapper">
	<section class="content">
		<div style="text-align: center">
			<h2>Success! Connected to QuickBooks Online</h2>
			<h3>Warning</h3>
			@if($result == 'not set')
				<h4>Your current Sales Tax Rate in QuickBooks is <strong>NOT SET</strong>.</h4>
				<h4>Please set your Sales Tax Rate in QuickBooks to <strong>{{ round($profile->tax->total / 100, 2) }}%</strong>. In order for Pockeyt to sync with Quickbooks, your Sales Taxes must match.</h4>
			@else
				<h4>Your current Sales Tax Rate in QuickBooks does <strong>NOT MATCH</strong> your Sales Tax in Pockeyt.</h4>
				<h4>Please set your Sales Tax Rate in QuickBooks to <strong>{{ round($profile->tax->total / 100, 2) }}%</strong>. If that is not the correct Sales Tax rate for your business please contact Pockeyt. In order for Pockeyt to sync with Quickbooks, your Sales Taxes must match.</h4>
			@endif
			<p>Please correct by adjusting your Sales Tax in QuickBooks or adjusting your location in Pockeyt*.</p>
			<p>Once your Sales Taxes are matching, click the Set Sales Tax Button in your Account Connections to finish setting up Pockeyt Sync.</p>
			<button type="button" class="btn btn-block btn-primary btn-lg" onclick="self.close()">Close Window</button>
			<p>*Pockeyt automatically sets your Sales Tax based on your businesses location.</p>
		</div>
	</section>
</div>
@stop

@section('scripts.footer')
<script type="text/javascript">
  window.opener.location.reload(false);
</script>