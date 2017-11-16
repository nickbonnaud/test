<form method="POST" action="{{ route('events.store', ['profiles' => $profile->slug]) }}" enctype="multipart/form-data">
  <div class="box-body">
    {{ csrf_field() }}
    <div class="form-group">
      <label for="title">Title:</label>
      <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
    </div>

    <div class="form-group">
      <label for="body">Message:</label>
      <textarea type="text" name="body" id="body" class="form-control" rows="3" required></textarea>
    </div>

    <div class="photo-input">
      <label for="photo">Add Photo</label>
      <input type="file" name="photo" id="photo" required>
      <p class="help-block">Required photo</p>
    </div>

    <div class="form-group">
      <label>Event Date:</label>
      <div class="input-group date">
      	<div class="input-group-addon">
      		<i class="fa fa-calendar"></i>
      	</div>
      	<input type="text" class="form-control pull-right" id="event_date_pretty">
      </div>
    </div>

    <input type="hidden" id="event_date" name="event_date" required>
  </div>

  <div class="box-footer">
    <button type="submit" class="btn btn-primary">Create Your Event!</button>
  </div>
</form>