<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\Filters\UserLocationFilters;
use App\UserLocation;
use App\Http\Resources\UserLocationResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayCustomersController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function index(UserLocationFilters $userLocationFilters, Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$profile = $user->profile;

		$userLocations = UserLocation::filter($userLocationFilters, $profile)->with('user')->get();
  	return UserLocationResource::collection($userLocations);
	}
}
