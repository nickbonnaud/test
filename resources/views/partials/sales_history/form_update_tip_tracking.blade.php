<form method="POST" action="{{ route('webApiProfiles.update', ['profiles' => $profile->slug]) }}">
	{{ method_field('PATCH') }}
	{{ csrf_field() }}
	@if($profile->tip_tracking_enabled)
		<input type="hidden" name="tip_tracking_enabled" value="0">
		<button type="submit" class="btn pull-right btn-primary quick-button">Disable Tip Tracking</button>
	@else
		<input type="hidden" name="tip_tracking_enabled" value="1">
		<button type="submit" class="btn pull-right btn-primary quick-button">Enable Tip Tracking</button>
	@endif
</form>