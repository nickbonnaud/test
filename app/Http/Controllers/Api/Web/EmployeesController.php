<?php

namespace App\Http\Controllers\Api\Web;

use App\User;
use App\Profile;
use App\Filters\UserFilters;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeesController extends Controller
{
	public function __construct() {
		$this->middleware('auth');
	}

	public function index(Profile $profile, UserFilters $filters, Request $request) {
		$this->authorize('view', $profile);
		if ($request->input('dashboard')) {
			$users = User::filter($filters, $profile)->get();
		} else {
			$users = User::filter($filters)->get();
		}
		return response()->json(array('users' => $users));
	}

	public function update(Profile $profile, User $user, Request $request) {
		$this->authorize('update', $profile);
		$user->update($request->all());
		return response()->json($user);
	}
}
