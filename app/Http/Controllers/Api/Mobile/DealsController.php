<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\Transaction;
use App\Http\Resources\UserLocationResource;
use App\Events\CustomerRedeemItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DealsController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function update(Transaction $transaction, Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		if (!($transaction->user->id == $user->id)) return response()->json(['error' => 'unauthorized'], 401);
		if ($request->redeemed) {
			$transaction->update($request->all());
			$type = 'deal_redeemed';
		} else {
			$type = $request->issue;
		}
		$user = new UserLocationResource($user, $transaction->profile);
		event(new CustomerRedeemItem($user, $transaction->profile, $type));
		return response()->json(['success' => true], 200);
	}
}
