<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostAnalyticsTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_view_post_analytics_dashboard() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');

		$this->get("/analytics/posts/" . $profile->slug)->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/analytics/posts/" . $profile->slug)->assertStatus(403);
	}

	function test_post_analytics_dashboard_fetches_totalViews() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		$postAnalytics = create('App\PostAnalytics', ['profile_id' => $profile->id, 'viewed' => true]);
		$postAnalytics1 = create('App\PostAnalytics', ['profile_id' => $profile->id]);

		$response = $this->get("/api/web/analytics/posts/" . $profile->slug . '?totalViews=1&type=count')->getData();
		$this->assertEquals(1, $response);
	}

	function test_post_analytics_dashboard_fetches_totalPurchased() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		$PostAnalytics = create('App\PostAnalytics', ['profile_id' => $profile->id, 'transaction_resulted' => true]);
		$PostAnalytics1 = create('App\PostAnalytics', ['profile_id' => $profile->id]);

		$response = $this->get("/api/web/analytics/posts/" . $profile->slug . '?totalPurchases=1&type=count')->getData();
		$this->assertEquals(1, $response);
	}

	function test_post_analytics_dashboard_fetches_totalRevenue() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		$postAnalytics = create('App\PostAnalytics', ['profile_id' => $profile->id, 'transaction_resulted' => true, 'total_revenue' => 500]);
		$postAnalytics1 = create('App\PostAnalytics', ['profile_id' => $profile->id, 'transaction_resulted' => true, 'total_revenue' => 600]);

		$response = $this->get("/api/web/analytics/posts/" . $profile->slug . '?totalPurchases=1&type=sum&selector=total_revenue')->getData();
		$this->assertEquals($postAnalytics->total_revenue + $postAnalytics1->total_revenue, $response);
	}

	function test_post_analytics_dashboard_fetches_totalAnalyticsAll() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		$postAnalytics = create('App\PostAnalytics', ['profile_id' => $profile->id, 'transaction_resulted' => true, 'total_revenue' => 500]);
		$postAnalytics1 = create('App\PostAnalytics', ['profile_id' => $profile->id, 'transaction_resulted' => true, 'total_revenue' => 600]);
		$postAnalytics2 = create('App\PostAnalytics', ['transaction_resulted' => true, 'total_revenue' => 600]);

		$response = $this->get("/api/web/analytics/posts/" . $profile->slug . '?type=count')->getData();
		$this->assertEquals(2, $response);
	}

	function test_post_analytics_dashboard_fetches_top_ten_posts_week_interactions() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		create('App\Post', ['profile_id' => $profile->id, 'total_interactions' => 10]);
		create('App\Post', ['profile_id' => $profile->id, 'total_interactions' => 9]);
		create('App\Post', ['profile_id' => $profile->id, 'total_interactions' => 8]);
		create('App\Post', ['profile_id' => $profile->id, 'total_interactions' => 7, 'updated_at' => Carbon::now()->subMonth(5)]);
		create('App\Post', ['total_interactions' => 20]);

		$response = $this->get("/api/web/posts/analytics/" . $profile->slug . '?interactionsWeek=1&type=get')->getData();

		$this->assertCount(3, $response);
	}

	function test_post_analytics_dashboard_fetches_top_ten_posts_month_interactions() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		create('App\Post', ['profile_id' => $profile->id, 'total_interactions' => 10]);
		create('App\Post', ['profile_id' => $profile->id, 'total_interactions' => 9]);
		create('App\Post', ['profile_id' => $profile->id, 'total_interactions' => 8, 'updated_at' => Carbon::now()->subWeeks(2)]);
		create('App\Post', ['profile_id' => $profile->id, 'total_interactions' => 7, 'updated_at' => Carbon::now()->subMonth(5)]);
		create('App\Post', ['total_interactions' => 20]);

		$response = $this->get("/api/web/posts/analytics/" . $profile->slug . '?interactionsMonth=1&type=get')->getData();

		$this->assertCount(3, $response);
	}

	function test_post_analytics_dashboard_fetches_top_ten_posts_two_month_interactions() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		create('App\Post', ['profile_id' => $profile->id, 'total_interactions' => 10]);
		create('App\Post', ['profile_id' => $profile->id, 'total_interactions' => 9]);
		create('App\Post', ['profile_id' => $profile->id, 'total_interactions' => 8, 'updated_at' => Carbon::now()->subMonth()]);
		create('App\Post', ['profile_id' => $profile->id, 'total_interactions' => 7, 'updated_at' => Carbon::now()->subMonth(5)]);
		create('App\Post', ['total_interactions' => 20]);

		$response = $this->get("/api/web/posts/analytics/" . $profile->slug . '?interactionsTwoMonth=1&type=get')->getData();

		$this->assertCount(3, $response);
	}

	function test_post_analytics_dashboard_fetches_top_ten_posts_week_revenue() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		create('App\Post', ['profile_id' => $profile->id, 'total_revenue' => 100]);
		create('App\Post', ['profile_id' => $profile->id, 'total_revenue' => 900]);
		create('App\Post', ['profile_id' => $profile->id, 'total_revenue' => 800]);
		create('App\Post', ['profile_id' => $profile->id, 'total_revenue' => 700, 'updated_at' => Carbon::now()->subMonth(5)]);
		create('App\Post', ['total_revenue' => 200]);

		$response = $this->get("/api/web/posts/analytics/" . $profile->slug . '?revenueWeek=1&type=get')->getData();

		$this->assertCount(3, $response);
	}

	function test_post_analytics_dashboard_fetches_top_ten_posts_month_revenue() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		create('App\Post', ['profile_id' => $profile->id, 'total_revenue' => 100]);
		create('App\Post', ['profile_id' => $profile->id, 'total_revenue' => 900]);
		create('App\Post', ['profile_id' => $profile->id, 'total_revenue' => 800, 'updated_at' => Carbon::now()->subWeeks(2)]);
		create('App\Post', ['profile_id' => $profile->id, 'total_revenue' => 700, 'updated_at' => Carbon::now()->subMonth(5)]);
		create('App\Post', ['total_revenue' => 200]);

		$response = $this->get("/api/web/posts/analytics/" . $profile->slug . '?revenueMonth=1&type=get')->getData();

		$this->assertCount(3, $response);
	}

	function test_post_analytics_dashboard_fetches_top_ten_posts_two_month_revenue() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		create('App\Post', ['profile_id' => $profile->id, 'total_revenue' => 100]);
		create('App\Post', ['profile_id' => $profile->id, 'total_revenue' => 900]);
		create('App\Post', ['profile_id' => $profile->id, 'total_revenue' => 800, 'updated_at' => Carbon::now()->subMonth()]);
		create('App\Post', ['profile_id' => $profile->id, 'total_revenue' => 700, 'updated_at' => Carbon::now()->subMonth(5)]);
		create('App\Post', ['total_revenue' => 200]);

		$response = $this->get("/api/web/posts/analytics/" . $profile->slug . '?revenueTwoMonth=1&type=get')->getData();

		$this->assertCount(3, $response);
	}
}
