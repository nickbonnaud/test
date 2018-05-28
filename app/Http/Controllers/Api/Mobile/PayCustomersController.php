<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Filters\UserLocationFilters;
use App\UserLocation;
use App\Http\Resources\UserLocationResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// For Setup need to get from jwt
use App\Profile;

class PayCustomersController extends Controller {

	public function index(UserLocationFilters $userLocationFilters, Request $request) {
		// For Setup need to get from jwt
		$profile = Profile::where('id', 1)->first();


		$userLocations = UserLocation::filter($userLocationFilters, $profile)->with('user')->get();
  	return UserLocationResource::collection($userLocations);
	}
}
