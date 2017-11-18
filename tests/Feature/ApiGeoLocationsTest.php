<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use App\Events\CustomerBreakGeoFence;
use App\Notifications\CustomerEnterGeoFence;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiGeoLocationsTest extends TestCase
{
	use RefreshDatabase;

	function test_a_mobile_user_is_cannot_get_user_locations_when_not_authenticated() {
		$this->withExceptionHandling();
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');
		$profile = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);

		$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id]);

		$data = [
			'is_heartbeat' => false,
			'coords' => [
				'latitude' => 35.925936,
				'longitude' => -79.040263
			]	
		];

		$this->json('POST', "/api/mobile/location?city={$city->slug}", $data)->assertStatus(401);
	}

	function test_a_mobile_user_is_returned_locations_when_not_in_geofence_radius_not_heartbeat() {
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');
		$profile = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);

		$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id]);

		$data = [
			'is_heartbeat' => false,
			'coords' => [
				'latitude' => 35.925936,
				'longitude' => -79.040263
			]	
		];

		$response = $this->json('POST', "/api/mobile/location?city={$city->slug}", $data, $this->headers($user))->getData();
		$this->assertCount(0, $response->data);
	}

	function test_a_mobile_user_is_returned_locations_when_in_geofence_radius_not_heartbeat() {
		Notification::fake();
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');
		$profile = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$account = create('App\Account', ['profile_id' => $profile->id, 'status' => 'approved']);

		$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id]);

		$data = [
			'is_heartbeat' => false,
			'coords' => [
				'latitude' => 34.78172123,
				'longitude' => -78.65666912
			]	
		];

		$response = $this->json('POST', "/api/mobile/location?city={$city->slug}", $data, $this->headers($user))->getData();
		$this->assertCount(1, $response->data);
		$this->assertDatabaseHas('user_locations', ['user_id' => $user->id, 'profile_id' => $profile->id]);
	}

	function test_a_user_locations_removed_in_db_if_user_not_in_geofence() {
		Notification::fake();
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');
		$profile = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$account = create('App\Account', ['profile_id' => $profile->id]);

		$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id]);
		$userLocation = create('App\UserLocation', ['user_id' => $user->id, 'profile_id' => $profile->id]);
		$this->assertDatabaseHas('user_locations', ['user_id' => $user->id, 'profile_id' => $profile->id]);

		$data = [
			'is_heartbeat' => false,
			'coords' => [
				'latitude' => 35.925936,
				'longitude' => -79.040263
			]	
		];

		$response = $this->json('POST', "/api/mobile/location?city={$city->slug}", $data, $this->headers($user))->getData();
		$this->assertCount(0, $response->data);
		$this->assertDatabaseMissing('user_locations', ['user_id' => $user->id, 'profile_id' => $profile->id]);
	}

	function test_a_user_location_updated_at_is_updated_if_exists_and_user_in_location() {
		Notification::fake();
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');
		$profile = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$account = create('App\Account', ['profile_id' => $profile->id, 'status' => 'approved']);

		$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id]);
		$userLocation = create('App\UserLocation', ['user_id' => $user->id, 'profile_id' => $profile->id, 'updated_at' => Carbon::yesterday()]);

		$data = [
			'is_heartbeat' => false,
			'coords' => [
				'latitude' => 34.78172123,
				'longitude' => -78.65666912
			]	
		];

		$response = $this->json('POST', "/api/mobile/location?city={$city->slug}", $data, $this->headers($user))->getData();
		$this->assertCount(1, $response->data);
		$this->assertDatabaseHas('user_locations', ['user_id' => $user->id, 'profile_id' => $profile->id]);
		$this->assertNotEquals($userLocation->updated_at, $userLocation->fresh()->updated_at);
	}

	function test_a_notification_is_stored_when_user_breaks_geofence() {
		Notification::fake();
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');
		$profile = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$account = create('App\Account', ['profile_id' => $profile->id, 'status' => 'approved']);

		$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id]);

		$data = [
			'is_heartbeat' => false,
			'coords' => [
				'latitude' => 34.78172123,
				'longitude' => -78.65666912
			]	
		];

		$response = $this->json('POST', "/api/mobile/location?city={$city->slug}", $data, $this->headers($user))->getData();

		Notification::assertSentTo(
      [$user],
      CustomerEnterGeoFence::class
    );
		$this->assertDatabaseHas('user_locations', ['user_id' => $user->id, 'profile_id' => $profile->id]);
	}

	function test_user_break_geofence_enter_event_is_sent() {
		Notification::fake();
		$this->expectsEvents(CustomerBreakGeoFence::class);
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');
		$profile = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$account = create('App\Account', ['profile_id' => $profile->id, 'status' => 'approved']);

		$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id]);

		$data = [
			'is_heartbeat' => false,
			'coords' => [
				'latitude' => 34.78172123,
				'longitude' => -78.65666912
			]	
		];

		$response = $this->json('POST', "/api/mobile/location?city={$city->slug}", $data, $this->headers($user))->getData();

		$this->assertDatabaseHas('user_locations', ['user_id' => $user->id, 'profile_id' => $profile->id]);
	}

	function test_a_mobile_user_can_get_their_city_if_exists_in_db() {
		$city = create('App\City');

		$response = $this->get("/api/mobile/cities?lat=35.778075&lng=-78.637884")->getData();
		$this->assertEquals($city->name, $response->city->name);
	}

	function test_a_mobile_user_not_in_city_in_db_does_not_get_city() {
		$city = create('App\City');

		$response = $this->get("/api/mobile/cities?lat=35.913200&lng=-79.055845")->getData();
		$this->assertNull($response->city);
	}
}
