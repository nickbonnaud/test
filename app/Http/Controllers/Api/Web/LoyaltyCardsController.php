<?php

namespace App\Http\Controllers\Api\Web;

use Illuminate\Http\Request;
use App\Profile;
use App\LoyaltyCard;
use App\Http\Controllers\Controller;

class LoyaltyCardsController extends Controller
{
	public function __construct() {
		$this->middleware('auth');
	}

	public function update(Profile $profile, LoyaltyCard $loyaltyCard, Request $request) {
		$this->authorize('view', $profile);
		if ($request->redeem_reward && ($loyaltyCard->loyalty_program_id == $profile->loyaltyProgram->id) && ($loyaltyCard->unredeemed_rewards > 0)) {
			$loyaltyCard->sendRedeemRequestToCustomer();
		}
		return response()->json(['success' => true], 200);
	}
}
