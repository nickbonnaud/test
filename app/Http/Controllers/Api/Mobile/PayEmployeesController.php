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

	public function store(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$profile = $user->profile;
		$shouldCreateEmployee = $request->is_create;
		\Log::debug($shouldCreateEmployee);
		
		if ($shouldCreateEmployee) {
			\Log::debug("Inside true part of if");
			$profile->employees()->save(new Employee($request->except('is_create')));
		} else {
			\Log::debug("Inside false part of if");
			$employee = Employee::where('pos_employee_id', $request->pos_employee_id)->first();
			$employee->delete();
		}
		return response()->json(['success' => 'employees_updated'], 200);
	}
}
