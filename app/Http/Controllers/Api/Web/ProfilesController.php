<?php

namespace App\Http\Controllers\Api\Web;

use Illuminate\Http\Request;
use App\Profile;
use App\Http\Controllers\Controller;

class ProfilesController extends Controller
{
	public function __construct() {
		$this->middleware('auth');
	}

	public function update(Profile $profile, Request $request) {
		$this->authorize('view', $profile);
		$profile->update($request->all());
		return redirect()->back();
	}
}
