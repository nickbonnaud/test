<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\GeoLocation;
use App\Filters\GeoLocationFilters;
use App\Http\Resources\GeoLocationResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GeoLocationsController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function index(Request $request, GeoLocationFilters $filters) {
		$user = JWTAuth::parseToken()->authenticate();
		$geoLocations = GeoLocation::filter($filters)->whereHas('profile.account', function($query) {
			$query->where('status', '=', 'Boarded');
		})->get();
		$locations = GeoLocation::getLocationsInRadius($request->coords, $geoLocations, $user);
		return GeoLocationResource::collection($locations);
	}
}
