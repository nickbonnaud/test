<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\LoyaltyCard;
use App\Profile;
use App\Http\Resources\UserLocationResource;
use App\Events\CustomerRedeemItem;
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
		$loyaltyCard->update($request->all());
		$user = new UserLocationResource($user);
		event(new CustomerRedeemItem($user, $loyaltyCard->loyaltyProgram->profile, $type='loyalty_card'));
		return response()->json(['success' => true], 200);
	}
}
