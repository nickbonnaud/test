<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\LoyaltyCard;
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
}
