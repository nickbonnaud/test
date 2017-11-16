<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Validation\Rule;
use App\Rules\PasswordCheck;
use Illuminate\Http\Request;

class UsersController extends Controller
{
	public function __construct() {
    $this->middleware('auth', []);
  }

  public function show(User $user) {
  	$this->authorize('view', $user);
  	return view('users.show', compact('user'));
  }

  public function update(User $user, Request $request) {
  	$this->authorize('update', $user);
  	$this->validateUserInfo($request, $user);
		$user = $user->updateData($request->except('photo'), $request->file('photo'));
		return redirect()->route('users.show', ['users' => $user->id]);
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
      'password' => 'required|confirmed|min:9|max:72|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%=@&?]).*$/'
		]);
	}
}
