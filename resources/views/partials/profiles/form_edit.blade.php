<form method="POST" action="{{ route('profiles.update', ['profiles' => $profile->slug]) }}" class="form-horizontal">
  {{ method_field('PATCH') }}
  {{ csrf_field() }}
  <div class="form-group">
    <label for="business_name" class="col-sm-2 control-label">Business name</label>
    <div class="col-sm-10">
      <input type="text" name="business_name" class="form-control" id="business_name" value="{{ $user->profile->business_name }}" required="">
    </div>
  </div>
  <div class="form-group">
    <label for="website" class="col-sm-2 control-label">Website URL</label>
    <div class="col-sm-10">
      <input type="text" name="website" class="form-control" id="website" value="{{ $user->profile->website }}" required>
    </div>
  </div>
  <div class="form-group">
    <label for="description" class="col-sm-2 control-label">Business Description</label>
    <div class="col-sm-10">
      <textarea type="text" name="description" class="form-control" rows="5" id="description" required>{{ $user->profile->description }}</textarea>
    </div>
  </div>
  <div class="modal-footer modal-footer-form">
    <div class="form-group">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary btn-form-footer">Save changes</button>
    </div>
  </div>
</form>