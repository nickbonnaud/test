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
		if ($request->has('rating')) {
			$profiles = Profile::filter($filters)
				->where('approved', '=', true)
				->paginate(10)
				->appends(Input::except('page'));
		} else {
			$profiles = Profile::filter($filters)
				->where('approved', '=', true)
				->orderBy('business_name', 'ASC')
				->paginate(10)
				->appends(Input::except('page'));
		}
		return ProfileResource::collection($profiles);
	}
}
