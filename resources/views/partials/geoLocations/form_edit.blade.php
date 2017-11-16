<form method="POST" action="{{ route('geoLocation.update', ['geoLocations' => $profile->geoLocation->id]) }}" class="form-horizontal">
	{{ method_field('PATCH') }}
	{{ csrf_field() }}
	<input type="hidden" name="latitude" id="latitude">
	<input type="hidden" name="longitude" id="longitude">
	<input type="hidden" name="state" id="state">
	<input type="hidden" name="county" id="county">
	<input type="hidden" name="zip" id="zip">
	<div class="modal-footer modal-footer-form">
    <div class="form-group">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary btn-form-footer">Save changes</button>
    </div>
  </div>
</form>