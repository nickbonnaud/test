<?php

namespace App\Http\Controllers\Api\Mobile;

use Validator;
use JWTAuth;
use App\PostAnalytics;
use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller {

	public function store(Request $request) {
		$validator = Validator::make($request->all(), [
			'analytics.*.post_id' => 'required|integer|min:0',
			'analytics.*.profile_id' => 'required|integer|min:0',
			'analytics.*.user_id' => 'nullable|integer|min:0',
			'analytics.*.viewed' => 'boolean',
			'analytics.*.viewed_on' => 'date',
			'analytics.*.bookmarked' => 'boolean',
			'analytics.*.bookmarked_on' => 'date',
			'analytics.*.shared' => 'boolean',
			'analytics.*.shared_on' => 'date',
		]);

		if ($validator->fails()) {
			return response()->json(['success' => false], 401);
		}

		foreach ($request->analytics as $postAnalytic) {
			$post = Post::where('id', $postAnalytic['post_id'])->first();
			$post->updateAnalytics($postAnalytic);
			if ($postAnalytic['user_id']) {
				$storedAnalytic = PostAnalytics::where('user_id', $postAnalytic['user_id'])
					->where('post_id', $postAnalytic['post_id'])->first();
					if ($storedAnalytic) {
						$storedAnalytic->fill($postAnalytic)->save();
					} else {
						$storedAnalytic = PostAnalytics::create($postAnalytic);
					}
			}
		}
		return response()->json(['success' => true], 200);
	}
}
