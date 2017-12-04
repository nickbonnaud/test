<form method="POST" action="{{ route('accounts.store', ['profiles' => $profile->slug]) }}">
  {{ csrf_field() }}
	<div class="form-group">
    <label for="legal_biz_name">Legal Business Name</label>
    <input type="text" name="legal_biz_name" id="legal_biz_name" value="{{ old('legal_biz_name') }}" placeholder="Example, Inc." class="form-control" required>
	</div>
	<div class="form-group">
    <label for="business_type">Business Type</label>
    <select name="business_type" id="business_type" class="form-control" required>
      <option value="">Please select Business Type</option>
      <option value="0" {{ old('business_type') === '0' ? 'selected' : '' }}>Sole Proprietor</option>
      <option value="2" {{ old('business_type') === '2' ? 'selected' : '' }}>LLC</option>
      <option value="3" {{ old('business_type') === '3' ? 'selected' : '' }}>Partnership</option>
      <option value="1" {{ old('business_type') === '1' ? 'selected' : '' }}>Corporation</option>
      <option value="4" {{ old('business_type') === '4' ? 'selected' : '' }}>Association</option>
    </select>
	</div>
	<div class="form-group">
    <label for="biz_tax_id">Federal Tax ID (EIN)</label>
    <input v-mask="'##-#######'" v-model="biz_tax_id" type="tel" name="biz_tax_id" id="biz_tax_id" placeholder="12-3456789" class="form-control" required>
	</div>
	<div class="form-group">
    <label for="established">Date Business Established</label>
    <input type="date" name="established" class="form-control" id="established" value="{{ old('established') }}" required>
	</div>
	<div class="form-group">
    <label for="annual_cc_sales">Estimate of Annual Credit Card Sales</label>
    <money v-model="annual_cc_sales" v-bind="money" type="tel" name="annual_cc_sales" class="form-control" id="annual_cc_sales" value="{{ old('annual_cc_sales') }}" required></money>
	</div>
	<div class="form-group">
    <label for="biz_street_address">Business Street Address</label>
    <input type="text" name="biz_street_address" id="biz_street_address" value="{{ old('biz_street_address') }}" class="form-control" required>
	</div>
	<div class="form-group">
    <label for="biz_city">City</label>
    <input type="text" name="biz_city" id="biz_city" value="{{ old('biz_city') }}" class="form-control" required>
	</div>
	<div class="form-group">
    <label for="biz_state">State</label>
    <input v-mask="'AA'" v-model="biz_state" type="text" name="biz_state" id="biz_state" placeholder="NC" maxlength="2" class="form-control" required>
	</div>
	<div class="form-group">
    <label for="biz_zip">Zip</label>
    <input v-mask="'#####'" v-model="biz_zip" type="tel" name="biz_zip" id="biz_zip" placeholder="12345" class="form-control" required>
	</div>
	<div class="form-group">
    <label for="phone">Business Phone Number</label>
    <input v-mask="'(###) ###-####'" v-model="phone" type="tel" name="phone" id="phone" placeholder="(111) 222-3333" class="form-control" required>
	</div>
	<div class="form-group">
    <label for="account_email">Business Email</label>
    <input type="email" name="account_email" id="account_email" value="{{ $user->email }}" class="form-control" required>
	</div>

	<div class="form-group">
	  <button type="submit" class="btn btn-primary form-control">Next</button>
	</div>
</form>