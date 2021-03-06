<?php

namespace App\Http\Controllers\Api\Web;

use App\Profile;
use App\Post;
use Illuminate\Http\Request;
use App\Filters\PostFilters;
use App\Http\Controllers\Controller;

class PostsController extends Controller
{
  public function __construct() {
    $this->middleware('auth', ['only' => ['index']]);
  }

  public function index(Profile $profile, PostFilters $postFilters, Request $request) {
    $this->authorize('view', $profile);
  	$type = $request->input('type');
  	$selector = $request->input('selector');
  	$posts = Post::filter($postFilters, $profile)->$type($selector);
  	return response()->json($posts);
  }

  public function store(Request $request) {
    $body = $request->getContent();
    $isFacebook = false;
    if ($request->is('*/facebook')) {
      $isFacebook = true;
      $signature = $request->header('x-hub-signature');
      $expected = 'sha1=' . hash_hmac('sha1', $body, env('FB_SECRET'));
      if ($signature != $expected) return;
      $postData = json_decode($body, true);
    } else {
      $postData = json_decode($body);
    }
    Post::processSubscription($postData, $isFacebook);
  }

  public function verifyFacebook(Request $request) {
    if (($request->hub_mode == 'subscribe') && ($request->hub_verify_token == env('FB_VERIFY_TOKEN'))) {
      return response($request->hub_challenge);
    }
  }

  public function verifyInstagram(Request $request) {
    if (($request->hub_mode == 'subscribe') && ($request->hub_verify_token == env('INSTA_VERIFY_TOKEN'))) {
      return response($request->hub_challenge);
    }
  }
}
