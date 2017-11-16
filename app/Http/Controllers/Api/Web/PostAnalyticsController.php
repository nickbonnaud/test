<?php

namespace App\Http\Controllers\Api\Web;

use App\Profile;
use App\PostAnalytics;
use Illuminate\Http\Request;
use App\Filters\PostAnalyticsFilters;
use App\Http\Controllers\Controller;

class PostAnalyticsController extends Controller
{
  public function __construct() {
    $this->middleware('auth');
  }

  public function index(Profile $profile, PostAnalyticsFilters $postAnalyticsFilters, Request $request) {
    $this->authorize('view', $profile);
  	$type = $request->input('type');
  	$selector = $request->input('selector');
    if ($selector) {
      $analytics = PostAnalytics::filter($postAnalyticsFilters, $profile)->$type($selector);
    } else {
      $analytics = PostAnalytics::filter($postAnalyticsFilters, $profile)->$type();
    }
  	
  	return response()->json($analytics);
  }
}
