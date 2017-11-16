<form method="POST" action="{{ route('users.update', ['users' => $user->id]) }}" class="form-horizontal">
	{{ method_field('PATCH') }}
  {{ csrf_field() }}
  <div class="form-group">
		<label for="old_password" class="col-sm-2 control-label">Current Password</label>
		<div class="col-sm-10">
			<input type="password" name="old_password" class="form-control" id="old_password" required>
		</div>
	</div>
	<div class="form-group">
		<label for="password" class="col-sm-2 control-label">New Password</label>
		<div class="col-sm-10">
			<input type="password" name="password" class="form-control" id="password" required>
			<p class="form-text text-muted">Password must be at least 9 characters long and contain 3 of the 4 categories: uppercase, lowercase, numbers, special characters.</p>
		</div>
	</div>
	<div class="form-group">
		<label for="password_confirmation" class="col-sm-2 control-label">Confirm Password</label>
		<div class="col-sm-10">
			<input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
		</div>
	</div>
	<div class="modal-footer modal-footer-form-tags">
		<div class="form-group">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-primary btn-form-footer">Save changes</button>
		</div>
	</div>
</form>