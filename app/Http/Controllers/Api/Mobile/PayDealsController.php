<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\Transaction;
use App\UserLocation;
use App\Http\Resources\PayCustomerResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayDealsController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function update(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$profile = $user->profile;
		
		$deal = Transaction::where('id', $request->id)->first();

		if (!$deal) {
			return response()->json(['error' => 'unable_to_find_deal'], 404);
		} elseif ($deal->redeemed) {
			return response()->json(['error' => 'deal_already_redeemed'], 403);
		} elseif ($deal->profile_id != $profile->id) {
			return response()->json(['error' => 'deal_not_owned_by_business'], 403);
		}
		$deal->sendRedeemRequestToCustomer();
		return response()->json(['success' => 'waiting_customer_approval'], 200);
	}
}
