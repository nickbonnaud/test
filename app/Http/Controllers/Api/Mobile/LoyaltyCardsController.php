<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\LoyaltyCard;
use App\Profile;
use App\UserLocation;
use App\Http\Resources\UserLocationResource;
use App\Http\Resources\PayCustomerResource;
use App\Events\CustomerRedeemItem;
use App\Events\UpdateConnectedApps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Resources\LoyaltyCardResource;
use App\Http\Controllers\Controller;

class LoyaltyCardsController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function index(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$cards = LoyaltyCard::where('user_id', '=', $user->id)->paginate(10)->appends(Input::except('page'));
		return LoyaltyCardResource::collection($cards);
	}

	public function update(LoyaltyCard $loyaltyCard, Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		if (!($loyaltyCard->user->id == $user->id)) return response()->json(['error' => 'unauthorized'], 401);

		if ($request->redeemed) {
			$loyaltyCard->subtractUnredeemedRewards();
			$type = 'loyalty_redeemed';
		} else {
			$type = $request->issue;
		}
		$userLocation = UserLocation::where('user_id', $user->id)->where('profile_id', $loyaltyCard->loyaltyProgram->profile->id)->first();
		$user = new UserLocationResource($userLocation);
		event(new CustomerRedeemItem($user, $loyaltyCard->loyaltyProgram->profile, $type));
		event(new UpdateConnectedApps($loyaltyCard->loyaltyProgram->profile, $type, new PayCustomerResource($userLocation)));
		return response()->json(['success' => true], 200);
	}
}
