<form method="POST" action="{{ route('deals.store', ['profiles' => $profile->slug]) }}" enctype="multipart/form-data">
	<div class="box-body">
	  {{ csrf_field() }}
		<div class="form-group">
	    <label for="message">Message:</label>
	    <textarea type="text" name="message" id="message" class="form-control" rows="3" required></textarea>
	  </div>
	  <div class="form-group">
	    <label for="deal_item">Purchase this post to receive:</label>
	    <input class="form-control" type="text" name="deal_item" id="deal_item" placeholder="Examples: Entrance to show or a limited time offer" required>
	  </div>
	  <div class="form-group">
	  	<label for="price">Price of Deal</label>
	  	<input-money type="price"></input-money>
	  </div>
	  <div class="photo-input">
	    <label for="photo">Add Photo</label>
	    <input type="file" name="photo" id="photo" required>
	    <p class="help-block">Required photo</p>
	  </div>

	  <div class="form-group">
	    <label>Last day of deal:</label>
	    <div class="input-group date">
	    	<div class="input-group-addon">
	    		<i class="fa fa-calendar"></i>
	    	</div>
	    	<input type="text" class="form-control pull-right" id="end_date_pretty" required>
	    </div>
	  </div>

	  <input type="hidden" id="end_date" name="end_date" required>
	  <input type="hidden" id="is_redeemable" name="is_redeemable" value='1' required>
	</div>

	<div class="box-footer">
	  <button type="submit" class="btn btn-primary">Create Your Deal!</button>
	</div>
</form>