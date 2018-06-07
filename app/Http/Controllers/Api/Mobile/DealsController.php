<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\Transaction;
use App\UserLocation;
use App\Http\Resources\UserLocationResource;
use App\Http\Resources\PayCustomerResource;
use App\Events\CustomerRedeemItem;
use App\Events\UpdateConnectedApps;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DealsController extends Controller {


	public function update(Transaction $transaction, Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		if (!($transaction->user->id == $user->id)) return response()->json(['error' => 'unauthorized'], 401);
		if ($request->redeemed) {
			$transaction->update($request->all());
			$type = 'deal_redeemed';
		} else {
			$type = $request->issue;
		}
		$userLocation = UserLocation::where('user_id', $user->id)->where('profile_id', $transaction->profile->id)->first();
		$user = new UserLocationResource($userLocation);
		event(new CustomerRedeemItem($user, $transaction->profile, $type));
		event(new UpdateConnectedApps($transaction->profile, $type, new PayCustomerResource($userLocation)));
		$transaction = $transaction->fresh();
		$dealId = [
			'deal_id' => $transaction->deal_id,
			'redeemed' => $transaction->redeemed,
			'created_at' => $transaction->created_at
		];
		return response()->json(['success' => true, 'dealId' => $dealId], 200);
	}

	public function test() {
		$userLocation = UserLocation::where('id', 5)->first();
		$profile = $userLocation->profile;
		$type = 'deal_redeemed';
		event(new UpdateConnectedApps($profile, $type, new PayCustomerResource($userLocation)));
	}
}
