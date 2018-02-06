<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PushTokenController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function store(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$pushToken = $user->pushToken();
		if ($pushToken) {
			$pushToken->push_token = $request->push_token;
			$pushToken->device = $request->device;
			$pushToken->save();
		} else {
			$user->pushToken()->create([
				'push_token' => $request->push_token,
				'device' => $request->device
			]);
		}
		return response()->json(['success' => true]);
	}
}
