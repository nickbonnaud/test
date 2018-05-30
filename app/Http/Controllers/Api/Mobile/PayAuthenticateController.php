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
		try {
			if (!$token = JWTAuth::attempt($credentials)) {
				return response()->json(['error' =>'invalid_email_or_password'], 422);
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
		// For Setup need to get from jwt
		$profile = Profile::where('id', 1)->first();
  	return new PayProfileResource($profile);
	}
}
