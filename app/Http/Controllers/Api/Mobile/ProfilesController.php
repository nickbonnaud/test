<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Profile;
use Illuminate\Http\Request;
use App\Filters\ProfileFilters;
use Illuminate\Support\Facades\Input;
use App\Http\Resources\ProfileResource;
use App\Http\Controllers\Controller;

class ProfilesController extends Controller {

	public function index(Request $request, ProfileFilters $filters) {
		$profiles = Profile::filter($filters)->paginate(10)->appends(Input::except('page'));
		return ProfileResource::collection($profiles);
	}
}
