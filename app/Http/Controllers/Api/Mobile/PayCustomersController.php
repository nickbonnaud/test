<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\Filters\UserLocationFilters;
use App\UserLocation;
use App\Http\Resources\PayCustomerResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Profile;

class PayCustomersController extends Controller {

	public function index(UserLocationFilters $userLocationFilters, Request $request) {
		$profile = Profile::where('id', 1)->first();

		$userLocations = UserLocation::filter($userLocationFilters, $profile)->with('user')->get();
  	return PayCustomerResource::collection($userLocations);
	}
}
