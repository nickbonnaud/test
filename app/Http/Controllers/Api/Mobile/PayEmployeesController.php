<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\Employee;
use Illuminate\Http\Request;
use App\Http\Resources\PayEmployeeResource;
use App\Http\Controllers\Controller;

class PayEmployeesController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function index(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$profile = $user->profile;

		$employees = Employee::where('profile_id', $profile->id)->get();
  	return PayEmployeeResource::collection($employees);
	}
}
