<form method="POST" action="{{ route('accounts.update', ['accounts' => $account->slug]) }}" class="form-horizontal">
	{{ method_field('PATCH') }}
  {{ csrf_field() }}
  <input type="hidden" name="type" value="business">
	<div class="form-group">
	  <label for="legal_biz_name" class="col-sm-2 control-label">Legal Name</label>
	  <div class="col-sm-10">
	    <input type="text" name="legal_biz_name" class="form-control" id="legal_biz_name" value="{{ $account->legal_biz_name }}" required>
	  </div>
	</div>
	<div class="form-group">
	  <label for="business_type" class="col-sm-2 control-label">Type</label>
	  <div class="col-sm-10">
	  	<select name="business_type" id="business_type" class="form-control" required>
	  		<option value="0" {{ ($account->business_type == 0) ? 'selected="selected"' : '' }}>Sole Proprietor</option>
	  		<option value="2" {{ ($account->business_type == 2) ? 'selected="selected"' : '' }}>LLC</option>
	  		<option value="3" {{ ($account->business_type == 3) ? 'selected="selected"' : '' }}>Partnership</option>
	  		<option value="4" {{ ($account->business_type == 4) ? 'selected="selected"' : '' }}>Association</option>
	  		<option value="1" {{ ($account->business_type == 1) ? 'selected="selected"' : '' }}>Corporation</option>
	  	</select>
	  </div>
	</div>
	<div class="form-group">
	 <label for="biz_tax_id" class="col-sm-2 control-label">Tax ID (EIN)</label>
	  <div class="col-sm-10">
	    <input v-mask="'##-#######'" v-model="biz_tax_id" type="tel" name="biz_tax_id" class="form-control" id="biz_tax_id" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="established" class="col-sm-2 control-label">Established</label>
	  <div class="col-sm-10">
	    <input type="date" name="established" class="form-control" id="established" value="{{ $account->established }}" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="annual_cc_sales" class="col-sm-2 control-label">Annual CC Sales</label>
	  <div class="col-sm-10">
	  	<input-money type="annual_cc_sales" :value="this.annual_cc_sales"></input-money>
	  </div>
	</div>
	<div class="form-group">
	 <label for="biz_street_address" class="col-sm-2 control-label">Business Address</label>
	  <div class="col-sm-10">
	    <input type="string" name="biz_street_address" class="form-control" id="biz_street_address" value="{{ $account->biz_street_address }}" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="biz_city" class="col-sm-2 control-label">City</label>
	  <div class="col-sm-10">
	    <input type="string" name="biz_city" class="form-control" id="biz_city" value="{{ $account->biz_city }}" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="biz_state" class="col-sm-2 control-label">State</label>
	  <div class="col-sm-10">
	    <input v-mask="'AA'" v-model="biz_state" type="string" name="biz_state" class="form-control" id="biz_state" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="biz_zip" class="col-sm-2 control-label">Zip</label>
	  <div class="col-sm-10">
	    <input v-mask="'#####'" v-model="biz_zip" type="tel" name="biz_zip" class="form-control" id="biz_zip" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="phone" class="col-sm-2 control-label">Business Phone</label>
	  <div class="col-sm-10">
	    <input v-mask="'(###) ###-####'" v-model="phone" type="tel" name="phone" class="form-control" id="phone" required>
	  </div>
	</div>
	<div class="form-group">
	 <label for="account_email" class="col-sm-2 control-label">Business Email</label>
	  <div class="col-sm-10">
	    <input type="email" name="account_email" class="form-control" id="account_email" value="{{ $account->account_email }}" required>
	  </div>
	</div>
	<div class="modal-footer modal-footer-form-tags">
	  <div class="form-group">
	    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	    <button type="submit" class="btn btn-primary btn-form-footer">Save changes</button>
	  </div>
	</div>
</form>