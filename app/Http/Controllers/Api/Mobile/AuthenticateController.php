<?php

namespace App\Http\Controllers\Api\Mobile;

use Carbon\Carbon;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions;
use Illuminate\Http\Request;
use App\Http\Requests\CreateApiUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthenticateController extends Controller {

	public function register(CreateApiUserRequest $request) {
		$user = $request->all();
		$user['password'] = Hash::make($request->input('password'));
		$user = User::create($user);
		$credentials = $request->only('email', 'password');

		try {
	    if (!$token = JWTAuth::attempt($credentials)) {
	      return response()->json(['error' => 'invalid_credentials'], 401);
	    }
		} catch (Exceptions\JWTException $e) {
	    // something went wrong whilst attempting to encode the token
	    return response()->json(['error' => 'could_not_create_token'], 500);
		}
		$user['token'] = $token;
		return response()->json(compact('user'));
	}

	public function login(Request $request) {
		$credentials = $request->only('email', 'password');

    try {
    	if (!$token = JWTAuth::attempt($credentials)) {
    		return response()->json(['error' =>'invalid_email_or_password'], 422);
    	}
    } catch (Exceptions\JWTException $e) {
    	return response()->json(['error' => 'failed_to_create_token'], 500);
    }
    $user = User::where('email', '=', $request->input('email'))->first();
    $user['token'] = $token;
    return response()->json(compact('user'));
	}

	public function me() {
		try {
      if (!$user = JWTAuth::parseToken()->authenticate()) {
      	return response()->json(['error' => 'user_not_found'], 404);
      }
    } catch (Exceptions\TokenExpiredException $e) {
        return response()->json(['error' => 'token_expired']);
    } catch (Exceptions\TokenInvalidException $e) {
        return response()->json(['error' => 'token_invalid']);
    } catch (Exceptions\JWTException $e) {
        return response()->json(['error' => 'token_absent']);
    }
    $user['token'] = [
    	'value' => JWTAuth::parseToken()->refresh(),
    	'expiry' => Carbon::now()->addMinutes(env('JWT_TTL'))->timestamp
    ];

    return response()->json(compact('user'));
	}
}
