<form method="POST" action="{{ route('accounts.update', ['accounts' => $account->slug]) }}">
	{{ method_field('PATCH') }}
  {{ csrf_field() }}
  <input type="hidden" name="type" value="owner">
	<div class="form-group">
	  <label for="account_user_first">Business Owner First:</label>
	  <input type="text" name="account_user_first" id="account_user_first" value="{{ $user->first_name }}" placeholder="First Name of Account Holder" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="account_user_last">Last:</label>
	  <input type="text" name="account_user_last" id="account_user_last" value="{{ $user->last_name }}" placeholder="Last Name of Account Holder" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="date_of_birth">Date of Birth</label>
	  <input type="date" name="date_of_birth" class="form-control" id="date_of_birth" value="{{ old('date_of_birth') }}" required>
	</div>

	<div class="form-group">
	  <label for="ownership">Percentage of Business Owned</label>
	  <input v-mask="'#?#?#'" v-model="ownership" type="tel" name="ownership" id="ownership" placeholder="100" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="indiv_street_address">Owner Home Address</label>
	  <input type="text" name="indiv_street_address" value="{{ old('indiv_street_address') }}" id="indiv_street_address" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="indiv_city">City</label>
	  <input type="text" name="indiv_city" id="indiv_city" value="{{ old('indiv_city') }}" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="indiv_state">State</label>
	  <input v-mask="'AA'" v-model="indiv_state" type="text" name="indiv_state" id="indiv_state" placeholder="NC" maxlength="2" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="indiv_zip">Zip</label>
	  <input v-mask="'#####'" v-model="indiv_zip" type="tel" name="indiv_zip" id="indiv_zip" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="owner_email">Owner Email:</label>
	  <input type="email" name="owner_email" id="owner_email" value="{{ $user->email }}" placeholder="Email of Owner" class="form-control" required>
	</div>

	<div class="form-group">
	    <label for="ssn">Owner SSN:</label>
	    <input v-mask="'###-##-####'" v-model="ssn" type="tel" name="ssn" id="ssn" class="form-control" required>
	</div>

	<div class="form-group">
	    <button type="submit" class="btn btn-primary form-control">Next</button>
	</div>
</form>