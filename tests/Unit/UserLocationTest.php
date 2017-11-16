<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use App\Events\CustomerBreakGeoFence;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserLocationTest extends TestCase
{
  use RefreshDatabase;

  function test_a_user_location_belongs_to_a_profile() {
    Notification::fake();
    $user = create('App\User');
    $profile = create('App\Profile');
    $account = create('App\Account', ['profile_id' => $profile->id]);
    $userLocation = create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user->id]);
    $this->assertInstanceOf('App\Profile', $userLocation->profile);
  }

  function test_a_user_location_belongs_to_a_user() {
    Notification::fake();
    $user = create('App\User');
    $profile = create('App\Profile');
    $account = create('App\Account', ['profile_id' => $profile->id]);
    $userLocation = create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user->id]);
    $this->assertInstanceOf('App\User', $userLocation->user);
  }

  function test_creating_userLocation_fires_geofence_event() {
    Notification::fake();
		$this->expectsEvents(CustomerBreakGeoFence::class);
    $user = create('App\User');
		$userLocation = create('App\UserLocation');
  }

  function test_deleting_a_userLocation_fires_geofence_event() {
    Notification::fake();
    $user = create('App\User');
		$profile = create('App\Profile');
    $account = create('App\Account', ['profile_id' => $profile->id]);
    $userLocation = create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user->id]);
		$this->expectsEvents(CustomerBreakGeoFence::class);
		$userLocation->delete();
  }
}
