<form method="POST" action="{{ route('posts.store', ['profiles' => $profile->slug]) }}" enctype="multipart/form-data">
	<div class="box-body">
	    {{ csrf_field() }}
	  <div class="form-group">
	      <label for="message">Message:</label>
	      <textarea type="text" name="message" id="message" class="form-control" rows="5" required></textarea>
	  </div>
	  <div class="photo-input">
	    <label for="photo">Add Photo</label>
	    <input type="file" name="photo" id="photo">
	    <p class="help-block">Optional photo</p>
	  </div>
	</div>

	<div class="box-footer">
	    <button type="submit" class="btn btn-primary">Create Your Post!</button>
	</div>
</form>