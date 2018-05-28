<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Profile;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayAuthenticateController extends Controller {

	public function me(Request $request) {
		// For Setup need to get from jwt
		$profile = Profile::where('id', 1)->get();
  	return ProfileResource::collection($profile);
	}
}
