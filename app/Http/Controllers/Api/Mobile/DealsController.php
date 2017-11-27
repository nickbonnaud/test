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
		$transaction->update($request->all());
		$user = new UserLocationResource($user);
		event(new CustomerRedeemItem($user, $transaction->profile, $type='deal'));
		return response()->json(['success' => true], 200);
	}
}
