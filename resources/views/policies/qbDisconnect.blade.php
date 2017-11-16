@extends('layouts.layoutBasic')
@section('content')
	<h3>Successfully Disconnected Pockeyt</h3>
	<p>Pockeyt is no longer connected to your QuickBooks account and will no longer auto-update transactions.</p>
	<p>To reconnect, please go to the <a href="{{ route('app.index') }}">Pockeyt Dashboard</a> and reconnect QuickBooks in your Inventory tab.</p>
	
@stop