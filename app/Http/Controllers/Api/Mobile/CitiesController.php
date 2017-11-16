<?php

namespace App\Http\Controllers\Api\Mobile;

use App\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CitiesController extends Controller {

	public function index(Request $request) {
		$city = City::getCurrentLocation($request->input('lat'), $request->input('lng'));
    return response()->json(['city' => $city]);
	}
}
