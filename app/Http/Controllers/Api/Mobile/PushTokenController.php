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
		$user->pushToken()->updateOrCreate(
			['push_token' => $request->push_token, 'device' => $request->device],
			['push_token' => $request->push_token]
		);
		return response()->json(['success' => true]);
	}
}
