<form method="POST" action="{{ route('accounts.update', ['accounts' => $account->slug]) }}">
	{{ method_field('PATCH') }}
  {{ csrf_field() }}
  <input type="hidden" name="type" value="bank">
	<div class="form-group">
	    <label for="method">Account Type</label>
	    <select name="method" id="method" class="form-control" required>
	        <option value="">Please select Account Type</option>
	        <option value="8" {{ old('method') === '8' ? 'selected' : '' }}>Checking Account</option>
	        <option value="9" {{ old('method') === '9' ? 'selected' : '' }}>Savings Account </option>
	        <option value="10" {{ old('method') === '10' ? 'selected' : '' }}>Corporate Checking Account</option>
	        <option value="11" {{ old('method') === '11' ? 'selected' : '' }}>Corporate Savings Account</option>
	    </select>
	</div>

	<div class="form-group">
	    <label for="account_number">Account Number</label>
	    <input type="tel" name="account_number" id="account_number" value="{{ old('account_number') }}" class="form-control" required>
	</div>

	<div class="form-group">
	    <label for="routing">Routing Number</label>
	    <input v-mask="'#########'" v-model="routing" type="tel" name="routing" id="routing" class="form-control" required>
	</div>

	<div class="form-group">
	  <div class="checkbox">
	    <label for="ToS">
	      <input type="checkbox" name="ToS" id="ToS" value="true" required>
	      Agree to <a href="#" data-toggle="modal" data-target="#ToSModal">Terms of Service and Privacy Policy</a>
	    </label>
	  </div>
	</div>
	<hr>
	
	<div class="form-group">
	    <button type="submit" class="btn btn-primary form-control">Finish</button>
	</div>
</form>
