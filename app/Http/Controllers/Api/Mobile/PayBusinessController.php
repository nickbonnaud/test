<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\Http\Resources\PayProfileResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayBusinessController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function update(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$profile = $user->profile;

		$profile->createOrUpdatePosAccount($request->all());
		return new PayProfileResource($profile);
	}
}
