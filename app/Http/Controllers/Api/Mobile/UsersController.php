<?php

namespace App\Http\Controllers\Api\Mobile;

use App\User;
use JWTAuth;
use Illuminate\Validation\Rule;
use App\Rules\PasswordCheck;
use App\Filters\UserFilters;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth')->except('index');
	}

	public function index(Request $request, UserFilters $filters) {
		$user = User::filter($filters);
		if ($request->query('unique')) {
			$unique = $user->count() == 0 ? true : false;
			return response()->json(['unique' => $unique]);
		}
		return response()->json(['user' => $user]);
	}

	public function update(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$this->validateUserInfo($request, $user);
		$user = $user->updateData($request->except('photo'), $request->file('photo'));

    $user['token'] = JWTAuth::fromUser($user);
		return response()->json(['user' => $user]);
	}


	
	// Validations for user
	public function validateUserInfo($request, $user) {
		if ($request->input('email')) {
			$this->validateEmailNameData($request, $user);
		} elseif($request->file('photo')){
			$this->validatePhoto($request);
		} elseif ($request->input('password')) {
			$this->validatePassword($request, $user);
		}
	}

	public function validateEmailNameData($request, $user) {
		$request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|max:255',
      'email' => [
      	'required',
      	'string',
      	'email',
      	'max:255',
      	Rule::unique('users')->ignore($user->id)
      ]
		]);
	}

	public function validatePhoto($request) {
		$request->validate([
      'photo' => 'mimes:jpg,jpeg,png,bmp',
		]);
	}

	public function validatePassword($request, $user) {
		$request->validate([
      'old_password' => ['required', new PasswordCheck($user)],
      'password' => 'required|confirmed|min:6|max:72'
		]);
	}
}
