<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use App\Events\CustomerBreakGeoFence;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserLocationTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_retrieve_a_profiles_customers_in_location() {
		Notification::fake();
    $this->withExceptionHandling();
  	$profile = create('App\Profile');
    $account = create('App\Account', ['profile_id' => $profile->id]);
  	$user = create('App\User');
  	$userLocation = create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user->id]);

  	$this->get("/api/web/location/customers/{$profile->slug}?default=1&type=get")->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/api/web/location/customers/{$profile->slug}?default=1&type=get")->assertStatus(403);
	}

	function test_an_authorized_user_can_retrieve_a_profiles_customers_in_location() {
		Notification::fake();
    $this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);
  	$user = create('App\User');

  	create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user->id]);
    create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user->id, 'updated_at' => Carbon::now()->subMinutes(11)]);

  	$response = $this->get("/api/web/location/customers/{$profile->slug}?default=1&type=get")->getData();
  	$this->assertCount(1, $response->data);
  	$this->assertEquals($user->id, $response->data[0]->id);
	}

  function test_an_authorized_user_retrieves_customers_in_correct_format() {
    Notification::fake();
    $this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);
    $user = create('App\User');

    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false]);

    $transactionOpen = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => false, 'refund_full' => false]);

    $post = create('App\Post', ['is_redeemable' => true, 'deal_item' => 'coffee', 'price' => 100]);
    create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'deal_id' => $post->id, 'redeemed' => false]);

    $postAnalyticOld = create('App\PostAnalytics', ['profile_id' => $profile->id, 'user_id' => $user->id, 'updated_at' => Carbon::now()->subDays(2)]);
    $postAnalytic = create('App\PostAnalytics', ['profile_id' => $profile->id, 'user_id' => $user->id]);

    create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user->id]);

    $response = $this->get("/api/web/location/customers/{$profile->slug}?default=1&type=get")->getData();
    $this->assertCount(1, $response->data);
    $this->assertEquals($user->id, $response->data[0]->id);
    $this->assertEquals($transaction->id, $response->data[0]->last_transaction->id);
    $this->assertEquals($transactionOpen->id, $response->data[0]->open_bill->id);
    $this->assertEquals($postAnalytic->id, $response->data[0]->last_post_interactions->id);
  }

  function test_user_break_geofence_triggers_pockeyt_lite_event_if_profile_enabled() {
    Notification::fake();
    $this->expectsEvents(CustomerBreakGeoFence::class);
    $user = create('App\User');
    $profile = create("App\Profile");
    $account = create('App\Account', ['profile_id' => $profile->id, 'pockeyt_lite_enabled' => true]);
    $userLocation = create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user->id]);

    $userLocation->delete();
  }
}
