<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagsController extends Controller {

	public function index(Request $request) {
		$tags = Tag::get();
		return response()->json(['tags' => $tags]);
	}
}
