<?php

namespace App\Http\Controllers\Api\Web;

use App\Profile;
use App\UserLocation;
use Illuminate\Http\Request;
use App\Filters\UserLocationFilters;
use App\Http\Resources\UserLocationResource;
use App\Http\Controllers\Controller;

class UserLocationsController extends Controller
{
  public function __construct() {
    $this->middleware('auth');
  }

  public function index(Profile $profile, UserLocationFilters $userLocationFilters, Request $request) {
    $this->authorize('view', $profile);
  	$type = $request->input('type');
  	$userLocations = UserLocation::filter($userLocationFilters, $profile)->with('user')->$type();
  	return UserLocationResource::collection($userLocations);
  }
}
