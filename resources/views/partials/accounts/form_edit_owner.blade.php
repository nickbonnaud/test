<form method="POST" action="{{ route('accounts.update', ['accounts' => $account->slug]) }}" class="form-horizontal">
  {{ method_field('PATCH') }}
  {{ csrf_field() }}
  <input type="hidden" name="type" value="owner">
  <div class="form-group">
    <label for="account_user_first" class="col-sm-2 control-label">First name</label>
    <div class="col-sm-10">
      <input type="text" name="account_user_first" class="form-control" id="account_user_first" value="{{ $account->account_user_first }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="account_user_last" class="col-sm-2 control-label">Last name</label>
    <div class="col-sm-10">
      <input type="text" name="account_user_last" class="form-control" id="account_user_last" value="{{ $account->account_user_last }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="date_of_birth" class="col-sm-2 control-label">DOB</label>
    <div class="col-sm-10">
      <input type="date" name="date_of_birth" class="form-control" id="date_of_birth" value="{{ $account->date_of_birth }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="ownership" class="col-sm-2 control-label">Percentage Ownership</label>
    <div class="col-sm-10">
      <input v-mask="'#?#?#'" v-model="ownership" type="tel" name="ownership" class="form-control" id="ownership" required>
    </div>
  </div>
  <div class="form-group">
   <label for="indiv_street_address" class="col-sm-2 control-label">Owner Address</label>
    <div class="col-sm-10">
      <input type="text" name="indiv_street_address" class="form-control" id="indiv_street_address" value="{{ $account->indiv_street_address }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="indiv_city" class="col-sm-2 control-label">City</label>
    <div class="col-sm-10">
      <input type="text" name="indiv_city" class="form-control" id="indiv_city" value="{{ $account->indiv_city }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="indiv_state" class="col-sm-2 control-label">State</label>
    <div class="col-sm-10">
      <input v-mask="'AA'" v-model="indiv_state" type="text" name="indiv_state" class="form-control" id="indiv_state" required>
    </div>
  </div>
  <div class="form-group">
   <label for="indiv_zip" class="col-sm-2 control-label">Zip</label>
    <div class="col-sm-10">
      <input v-mask="'#####'" v-model="indiv_zip" type="tel" name="indiv_zip" class="form-control" id="indiv_zip" required>
    </div>
  </div>
  <div class="form-group">
   <label for="owner_email" class="col-sm-2 control-label">Owner Email</label>
    <div class="col-sm-10">
      <input type="email" name="owner_email" class="form-control" id="owner_email" value="{{ $account->owner_email }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="ssn" class="col-sm-2 control-label">Full SSN</label>
    <div class="col-sm-10">
      <input v-mask="'NNN-NN-####'" v-model="ssn" type="tel" name="ssn" class="form-control" id="ssn" required>
    </div>
  </div>
  <div class="modal-footer modal-footer-form-tags">
    <div class="form-group">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary btn-form-footer">Save changes</button>
    </div>
  </div>
</form>
