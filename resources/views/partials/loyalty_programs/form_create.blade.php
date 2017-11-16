<form method="POST" action="{{ route('loyaltyProgram.store', ['profiles' => $profile->slug]) }}">
	{{ csrf_field() }}
	<div class="box-body">
		<div class="form-group">
			<div class="radio">
				<label>
					<input type="radio" name="optionsRadios" id="increment" value="increment" v-model="selection">
					Reward customers once they make a certain number of purchases
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="optionsRadios" id="amount" value="amount" v-model="selection">
					Or reward customers after they have spent a certain amount
				</label>
			</div>
		</div>
		<div class="form-group" v-if= "selection == 'increment'">
			<label for="purchases_required">Number of Purchases required for reward</label>
			<div class="input-group col-xs-5 col-md-3">
				<span class="input-group-addon">#</span>
				<input class="form-control" type="number" min="1" step="1" name="purchases_required" id="purchases_required" placeholder="20">
			</div>
		</div>
		<div class="form-group" v-if= "selection == 'amount'">
			<label for="amount_required">Total amount customers must spend to receive reward</label>
			<div class="input-group col-xs-5 col-md-3">
				<span class="input-group-addon">$</span>
				<input-money type="amount_required"></input-money>
			</div>
		</div>
		<div class="form-group">
			<label for="reward">Loyalty program reward</label>
			<p class="help-block">*Please just name reward</p>
			<div class="input-group col-xs-12 col-md-6">
				<span class="input-group-addon"><i class="fa fa-trophy"></i></span>
				<input class="form-control" type="text" name="reward" id="reward" placeholder='i.e. Free small coffee not A free small coffee' required>
			</div>
		</div>
	</div>
	<div class="box-footer">
	  <button type="submit" class="btn btn-success">Create</button>
	</div>
</form>