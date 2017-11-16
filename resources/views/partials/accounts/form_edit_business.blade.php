<form method="POST" action="{{ route('accounts.update', ['accounts' => $account->slug]) }}" class="form-horizontal">
	{{ method_field('PATCH') }}
  {{ csrf_field() }}
  <input type="hidden" name="type" value="business">
	<div class="form-group">
	  <label for="legalBizName" class="col-sm-2 control-label">Legal Name</label>
	  <div class="col-sm-10">
	    <input type="text" name="legalBizName" class="form-control" id="legalBizName" value="{{ $account->legalBizName }}" required>
	  </div>
	</div>
	<div class="form-group">
	  <label for="businessType" class="col-sm-2 control-label">Type</label>
	  <div class="col-sm-10">
	  	<select name="businessType" id="businessType" class="form-control" required>
	  		<option value="0" {{ ($account->businessType == 0) ? 'selected="selected"' : '' }}>Sole Proprietor</option>
	  		<option value="2" {{ ($account->businessType == 2) ? 'selected="selected"' : '' }}>LLC</option>
	  		<option value="3" {{ ($account->businessType == 3) ? 'selected="selected"' : '' }}>Partnership</option>
	  		<option value="4" {{ ($account->businessType == 4) ? 'selected="selected"' : '' }}>Association</option>
	  		<option value="1" {{ ($account->businessType == 1) ? 'selected="selected"' : '' }}>Corporation</option>
	  	</select>
	  </div>
	</div>
	<div class="form-group">
	 <label for="bizTaxId" class="col-sm-2 control-label">Tax ID (EIN)</label>
	  <div class="col-sm-10">
	    <input v-mask="'##-#######'" v-model="bizTaxId" type="tel" name="bizTaxId" class="form-control" id="bizTaxId" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="established" class="col-sm-2 control-label">Established</label>
	  <div class="col-sm-10">
	    <input type="date" name="established" class="form-control" id="established" value="{{ $account->established }}" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="annualCCSales" class="col-sm-2 control-label">Annual CC Sales</label>
	  <div class="col-sm-10">
	  	<input-money type="annualCCSales" :value="this.annualCCSales"></input-money>
	  </div>
	</div>
	<div class="form-group">
	 <label for="bizStreetAddress" class="col-sm-2 control-label">Business Address</label>
	  <div class="col-sm-10">
	    <input type="string" name="bizStreetAddress" class="form-control" id="bizStreetAddress" value="{{ $account->bizStreetAddress }}" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="bizCity" class="col-sm-2 control-label">City</label>
	  <div class="col-sm-10">
	    <input type="string" name="bizCity" class="form-control" id="bizCity" value="{{ $account->bizCity }}" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="bizState" class="col-sm-2 control-label">State</label>
	  <div class="col-sm-10">
	    <input v-mask="'AA'" v-model="bizState" type="string" name="bizState" class="form-control" id="bizState" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="bizZip" class="col-sm-2 control-label">Zip</label>
	  <div class="col-sm-10">
	    <input v-mask="'#####'" v-model="bizZip" type="tel" name="bizZip" class="form-control" id="bizZip" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="phone" class="col-sm-2 control-label">Business Phone</label>
	  <div class="col-sm-10">
	    <input v-mask="'(###) ###-####'" v-model="phone" type="tel" name="phone" class="form-control" id="phone" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="accountEmail" class="col-sm-2 control-label">Business Email</label>
	  <div class="col-sm-10">
	    <input type="email" name="accountEmail" class="form-control" id="accountEmail" value="{{ $account->accountEmail }}" required>
	  </div>
	</div>
	<div class="modal-footer modal-footer-form-tags">
	  <div class="form-group">
	    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	    <button type="submit" class="btn btn-primary btn-form-footer">Save changes</button>
	  </div>
	</div>
</form>