<?php

namespace App\Http\Controllers;

use App\Profile;
use App\PostAnalytics;
use App\Filters\PostAnalyticsFilters;
use Illuminate\Http\Request;

class AnalyticsDashboardController extends Controller
{
  public function __construct() {
		$this->middleware('auth');
	}

	public function show(Profile $profile, PostAnalyticsFilters $postAnalyticsFilters) {
		$this->authorize('view', $profile);
		$totalViews = PostAnalytics::filter($postAnalyticsFilters, $profile, $type = 'totalViews')->count();
		$totalRevenue = PostAnalytics::filter($postAnalyticsFilters, $profile, $type = 'totalPurchases')->sum('total_revenue');
		return view('dashboard_analytics.show', compact('totalViews', 'totalRevenue'));
	}
}
