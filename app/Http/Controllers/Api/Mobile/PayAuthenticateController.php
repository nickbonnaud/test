<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Resources\UserLocationResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// For Setup need to get from jwt
use App\Profile;

class PayAuthenticateController extends Controller {

	public function me(Request $request) {
		// For Setup need to get from jwt
		$profile = Profile::where('id', 1)->first();
  	return response()->json(['business' => $profile]);
	}
}
