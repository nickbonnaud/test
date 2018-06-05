<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Profile;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions;
use App\Http\Resources\PayProfileResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayAuthenticateController extends Controller {

	public function login(Request $request) {
		$credentials = $request->only('email', 'password');
		if (!$user = User::where('email', $request->email)->first()) {
			return response()->json(['error' =>'invalid_email'], 422);
		}
		try {
			if (!$token = JWTAuth::attempt($credentials)) {
				return response()->json(['error' =>'invalid_password'], 422);
			}
		} catch (Exceptions\JWTException $e) {
			return response()->json(['error' => 'failed_to_create_token'], 500);
		}
		$user = User::where('email', $request->input('email'))->first();
		$profile = $user->profile;
		$profile['token'] = $token;
		return new PayProfileResource($profile);
	}

	public function me(Request $request) {
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['error' => 'user_not_found'], 404);
			}
		} catch (Exceptions\TokenExpiredException $e) {
			return response()->json(['error' => 'token_expired'], 401);
		} catch (Exceptions\TokenInvalidException $e) {
			return response()->json(['error' => 'token_invalid'], 401);
		} catch (Exceptions\JWTException $e) {
			return response()->json(['error' => 'token_absent'], 401);
		}
		$profile = $user->profile;
		$profile['token'] = JWTAuth::parseToken()->refresh();
  	return new PayProfileResource($profile);
	}
}
