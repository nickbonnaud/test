<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\GeoLocation;
use App\Filters\GeoLocationFilters;
use App\Http\Resources\GeoLocationResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GeoFenceController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function index(Request $request, GeoLocationFilters $filters) {
		$geoLocations = GeoLocation::filter($filters)->whereHas('profile.account', function($query) {
			$query->where('status', '=', 'Boarded');
		})->get();
		$coords = ['latitude' => $request->lat, 'longitude' => $request->lng];
		$locations = GeoLocation::getLocationsInRadius($coords, $geoLocations);
		return GeoLocationResource::collection($locations);
	}
}