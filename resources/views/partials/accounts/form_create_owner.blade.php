<form method="POST" action="{{ route('accounts.update', ['accounts' => $account->slug]) }}">
	{{ method_field('PATCH') }}
  {{ csrf_field() }}
  <input type="hidden" name="type" value="owner">
	<div class="form-group">
	  <label for="accountUserFirst">Business Owner First:</label>
	  <input type="text" name="accountUserFirst" id="accountUserFirst" value="{{ $user->first_name }}" placeholder="First Name of Account Holder" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="accountUserLast">Last:</label>
	  <input type="text" name="accountUserLast" id="accountUserLast" value="{{ $user->last_name }}" placeholder="Last Name of Account Holder" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="dateOfBirth">Date of Birth</label>
	  <input type="date" name="dateOfBirth" class="form-control" id="dateOfBirth" value="{{ old('dateOfBirth') }}" required>
	</div>

	<div class="form-group">
	  <label for="ownership">Percentage of Business Owned</label>
	  <input v-mask="'#?#?#'" v-model="ownership" type="tel" name="ownership" id="ownership" placeholder="100" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="indivStreetAddress">Owner Home Address</label>
	  <input type="text" name="indivStreetAddress" value="{{ old('indivStreetAddress') }}" id="indivStreetAddress" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="indivCity">City</label>
	  <input type="text" name="indivCity" id="indivCity" value="{{ old('indivCity') }}" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="indivState">State</label>
	  <input v-mask="'AA'" v-model="indivState" type="text" name="indivState" id="indivState" placeholder="NC" maxlength="2" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="indivZip">Zip</label>
	  <input v-mask="'#####'" v-model="indivZip" type="tel" name="indivZip" id="indivZip" class="form-control" required>
	</div>

	<div class="form-group">
	  <label for="ownerEmail">Owner Email:</label>
	  <input type="email" name="ownerEmail" id="ownerEmail" value="{{ $user->email }}" placeholder="Email of Owner" class="form-control" required>
	</div>

	<div class="form-group">
	    <label for="ssn">Owner SSN:</label>
	    <input v-mask="'###-##-####'" v-model="ssn" type="tel" name="ssn" id="ssn" class="form-control" required>
	</div>

	<div class="form-group">
	    <button type="submit" class="btn btn-primary form-control">Next</button>
	</div>
</form>