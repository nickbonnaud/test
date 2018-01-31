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
		$user->pushToken()->create($request->all());
		return response()->json(['success' => true]);
	}
}
