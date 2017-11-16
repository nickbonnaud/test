<form method="POST" action="{{ route('accounts.update', ['accounts' => $account->slug]) }}" class="form-horizontal">
  {{ method_field('PATCH') }}
  {{ csrf_field() }}
  <input type="hidden" name="type" value="owner">
  <div class="form-group">
    <label for="accountUserFirst" class="col-sm-2 control-label">First name</label>
    <div class="col-sm-10">
      <input type="text" name="accountUserFirst" class="form-control" id="accountUserFirst" value="{{ $account->accountUserFirst }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="accountUserLast" class="col-sm-2 control-label">Last name</label>
    <div class="col-sm-10">
      <input type="text" name="accountUserLast" class="form-control" id="accountUserLast" value="{{ $account->accountUserLast }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="dateOfBirth" class="col-sm-2 control-label">DOB</label>
    <div class="col-sm-10">
      <input type="date" name="dateOfBirth" class="form-control" id="dateOfBirth" value="{{ $account->dateOfBirth }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="ownership" class="col-sm-2 control-label">Percentage Ownership</label>
    <div class="col-sm-10">
      <input v-mask="'#?#?#'" v-model="ownership" type="tel" name="ownership" class="form-control" id="ownership" required>
    </div>
  </div>
  <div class="form-group">
   <label for="indivStreetAddress" class="col-sm-2 control-label">Owner Address</label>
    <div class="col-sm-10">
      <input type="text" name="indivStreetAddress" class="form-control" id="indivStreetAddress" value="{{ $account->indivStreetAddress }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="indivCity" class="col-sm-2 control-label">City</label>
    <div class="col-sm-10">
      <input type="text" name="indivCity" class="form-control" id="indivCity" value="{{ $account->indivCity }}" required>
    </div>
  </div>
  <div class="form-group">
   <label for="indivState" class="col-sm-2 control-label">State</label>
    <div class="col-sm-10">
      <input v-mask="'AA'" v-model="indivState" type="text" name="indivState" class="form-control" id="indivState" required>
    </div>
  </div>
  <div class="form-group">
   <label for="indivZip" class="col-sm-2 control-label">Zip</label>
    <div class="col-sm-10">
      <input v-mask="'#####'" v-model="indivZip" type="tel" name="indivZip" class="form-control" id="indivZip" required>
    </div>
  </div>
  <div class="form-group">
   <label for="ownerEmail" class="col-sm-2 control-label">Owner Email</label>
    <div class="col-sm-10">
      <input type="email" name="ownerEmail" class="form-control" id="ownerEmail" value="{{ $account->ownerEmail }}" required>
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
