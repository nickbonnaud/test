<form method="POST" action="{{ route('accounts.update', ['accounts' => $account->slug]) }}" class="form-horizontal">
	{{ method_field('PATCH') }}
  {{ csrf_field() }}
  <input type="hidden" name="type" value="bank">
	<div class="form-group">
	  <label for="method" class="col-sm-2 control-label">Account Type</label>
	  <div class="col-sm-10">
	  	<select name="method" id="method" class="form-control" required>
		  	<option value="8" {{ ($account->method == 8) ? 'selected="selected"' : '' }}>Checking Account</option>
		  	<option value="9" {{ ($account->method == 9) ? 'selected="selected"' : '' }}>Savings Account</option>
		  	<option value="10" {{ ($account->method == 10) ? 'selected="selected"' : '' }}>Corporate Checking Account</option>
		  	<option value="11" {{ ($account->method == 11) ? 'selected="selected"' : '' }}>Corporate Savings Account</option>
	  	</select>
	  </div>
	</div>

	<div class="form-group">
	  <label for="account_number" class="col-sm-2 control-label">Account Number</label>
	  <div class="col-sm-10">
	    <input type="tel" name="account_number" class="form-control" value="XXXXX{{$account->account_number}}" id="account_number" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="routing" class="col-sm-2 control-label">Routing Number</label>
	  <div class="col-sm-10">
	    <input v-mask="'NNNNN####'" v-model="routing" type="tel" name="routing" class="form-control" id="routing" required>
	  </div>
	</div>
	<div class="modal-footer modal-footer-form-tags">
	  <div class="form-group">
	    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	    <button type="submit" class="btn btn-primary btn-form-footer">Save changes</button>
	  </div>
	</div>
</form>