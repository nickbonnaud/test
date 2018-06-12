<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\LoyaltyCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayLoyaltyCardsController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function update(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$profile = $user->profile;
		
		$loyaltyCard = LoyaltyCard::where('id', $request->id)->first();

		if (!$loyaltyCard) {
			return response()->json(['error' => 'unable_to_find_loyalty_card'], 404);
		} elseif ($loyaltyCard->unredeemed_rewards == 0) {
			return response()->json(['error' => 'no_rewards_to_redeem'], 403);
		} elseif ($loyaltyCard->loyaltyProgram->profile_id != $profile->id) {
			return response()->json(['error' => 'loyalty_card_not_owned_by_business'], 403);
		}
		$loyaltyCard->sendRedeemRequestToCustomer();
		return response()->json(['success' => 'waiting_customer_approval'], 200);
	}
}
