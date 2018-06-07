<?php

namespace App\Http\Controllers\Api\Mobile;

use Pusher\Pusher;
use JWTAuth;
use App\User;
use App\Profile;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Http\Controllers\Controller;

class PusherController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function authenticate(Request $request, User $user) {
		$tokenUser = JWTAuth::parseToken()->authenticate();
		if ($tokenUser->id !== $user->id) {
			throw new AccessDeniedHttpException;
		}
		
		$pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'));
		$pusherAuth = $pusher->socket_auth($request->channel_name, $request->socket_id);
		return response($pusherAuth);
	}

	public function authenticateBusiness(Request $request, Profile $profile) {
		$tokenUser = JWTAuth::parseToken()->authenticate();
		if ($tokenUser->id !== $profile->user->id) {
			throw new AccessDeniedHttpException;
		}

		$pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'));
		$pusherAuth = $pusher->socket_auth($request->channel_name, $request->socket_id);
		return response($pusherAuth);
	}
}
