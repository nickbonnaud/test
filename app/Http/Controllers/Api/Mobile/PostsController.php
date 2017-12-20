<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Post;
use Illuminate\Http\Request;
use App\Filters\PostFilters;
use Illuminate\Support\Facades\Input;
use App\Http\Resources\PostResource;
use App\Http\Controllers\Controller;

class PostsController extends Controller {

	public function index(Request $request, PostFilters $filters) {
		$posts = Post::apiFilter($filters)->whereHas('profile', function($query) {
			$query->where('approved', '=', true);
		})->with('profile')->paginate(10)->appends(Input::except('page'));
		return PostResource::collection($posts);
	}
}
