<form method="POST" action="{{ route('profiles.store') }}">
	{{ csrf_field() }}
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
	  <input id="pac-input" class="controls" type="text" placeholder="Enter a location">
	  <div id="map"></div>
	</div>

	<input type="hidden" name="latitude" id="latitude">
	<input type="hidden" name="longitude" id="longitude">
	<input type="hidden" name="state" id="state">
	<input type="hidden" name="county" id="county">
	<input type="hidden" name="city" id="city">

  <div class="form-group">
    <label for="tags">Tags</label>
    <select name="tags[]" id="tags" class="form-control" multiple="multiple" required>
    	@foreach ($tags as $tag)
    		<option value="{{ $tag->id }}">{{ $tag->name }}</option>
    	@endforeach
    </select>
  </div>

	<hr>

	<div class="form-group">
	    <button type="submit" class="btn btn-info form-control">Save</button>
	</div>
</form>