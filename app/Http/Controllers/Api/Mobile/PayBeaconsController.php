<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use Webpatser\Uuid\Uuid;
use App\Http\Resources\PayBeaconResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayBeaconsController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function index(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$profile = $user->profile;
		
		if (!$beacon = $profile->beacon) {
			$beacon = $this->createBeacon($profile);
		}

		return new PayBeaconResource($beacon);
	}


	private function createBeacon($profile) {
		return $profile->beacon()->create([
			'uuid' => Uuid::generate(4)->string,
			'identifier' => $profile->slug,
		]);
	}
}
