<form method="POST" action="{{ route('users.update', ['users' => $user->id]) }}" class="form-horizontal">
	{{ method_field('PATCH') }}
  {{ csrf_field() }}
  <div class="form-group">
		<label for="first_name" class="col-sm-2 control-label">First name</label>
		<div class="col-sm-10">
			<input type="text" name="first_name" class="form-control" id="first_name" value="{{ $user->first_name }}" required="">
		</div>
	</div>
	<div class="form-group">
		<label for="last_name" class="col-sm-2 control-label">Last name</label>
		<div class="col-sm-10">
			<input type="text" name="last_name" class="form-control" id="last_name" value="{{ $user->last_name }}" required>
		</div>
	</div>
	<div class="form-group">
		<label for="email" class="col-sm-2 control-label">Email</label>
		<div class="col-sm-10">
			<input type="text" name="email" class="form-control" id="email" value="{{ $user->email }}" required>
		</div>
	</div>
	<div class="modal-footer modal-footer-form-tags">
		<div class="form-group">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-primary btn-form-footer">Save changes</button>
		</div>
	</div>
</form>