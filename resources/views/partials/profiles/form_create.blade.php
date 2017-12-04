<form method="POST" action="{{ route('profiles.store') }}">
	{{ csrf_field() }}
	<div class="form-group">
	  <input id="pac-input" class="controls" type="text" placeholder="Enter a location">
	  <div id="map"></div>
	</div>

	<div class="form-group">
	  <label for="business_name">Business Name:</label>
	  <input type="text" name="business_name" id="business_name" class="form-control" value="{{ old('business_name') }}" required autofocus>
	</div>

	<div class="form-group">
    <label for="website">Website URL:</label>
    <input type="text" name="website" id="website" class="form-control" placeholder="www.example.com" value="{{ old('website') }}" required>
	</div>

	<div class="form-group">
    <label for="description">Business Description:</label>
    <textarea name="description" id="description" class="form-control" rows="10" required>{{ old('description') }}</textarea>
	</div>

  <div class="form-group">
    <label for="tags">Tags</label>
    <select name="tags[]" id="tags" class="form-control" multiple="multiple" required>
    	@foreach ($tags as $tag)
    		<option value="{{ $tag->id }}">{{ $tag->name }}</option>
    	@endforeach
    </select>
  </div>

	<hr>

	<input type="hidden" name="latitude" id="latitude">
	<input type="hidden" name="longitude" id="longitude">
	<input type="hidden" name="biz_street_address" id="biz_street_address">
	<input type="hidden" name="biz_city" id="biz_city">
	<input type="hidden" name="biz_state" id="biz_state">
	<input type="hidden" name="biz_county" id="biz_county">
	<input type="hidden" name="biz_zip" id="biz_zip">
	<input type="hidden" name="phone" id="phone">
	<input type="hidden" name="google_rating" id="google_rating">
	<input type="hidden" name="google_id" id="google_id">

	<div class="form-group">
	    <button type="submit" class="btn btn-info form-control">Save</button>
	</div>
</form>