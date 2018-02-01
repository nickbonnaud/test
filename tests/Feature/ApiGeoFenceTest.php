<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use App\Events\CustomerBreakGeoFence;
use App\Notifications\CustomerEnterGeoFence;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiGeoFenceTest extends TestCase
{
	use RefreshDatabase;

	function test_a_mobile_user_cannot_get_geofences_if_not_authenticated() {
		$this->withExceptionHandling();
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');

		$profile = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id]);

		$lat = 35.925936;
		$lng = -79.040263;

		$this->get("/api/mobile/geofences?city={$city->slug}&lat={$lat}&lng=${lng}")->assertStatus(401);
	}

	function test_an_authorized_mobile_user_is_returned_geo_fences_only_in_radius() {
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');
		$city1 = create('App\City', ['name' => 'raleigh']);

		$profileInRadius = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$account = create('App\Account', ['profile_id' => $profileInRadius->id, 'status' => 'boarded']);
		$geoLocationIn = create('App\GeoLocation', ['profile_id' => $profileInRadius->id]);

		$profileNotInRadius = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$account = create('App\Account', ['profile_id' => $profileNotInRadius->id, 'status' => 'boarded']);
		$geoLocationOut = create('App\GeoLocation', ['profile_id' => $profileNotInRadius->id, 'latitude' => 30.78172123, 'longitude' => -75.65666912]);

		$profileNotInCity= create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city1->id]);
		$account = create('App\Account', ['profile_id' => $profileNotInCity->id, 'status' => 'boarded']);
		$geoLocationOut = create('App\GeoLocation', ['profile_id' => $profileNotInCity->id, 'latitude' => 30.78172123, 'longitude' => -75.65666912]);

		$profileInRadius2 = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$account = create('App\Account', ['profile_id' => $profileInRadius2->id, 'status' => 'boarded']);
		$geoLocationIn = create('App\GeoLocation', ['profile_id' => $profileInRadius2->id]);
		
		$lat = $geoLocationIn->latitude;
		$lng = $geoLocationIn->longitude;

		$response = $this->get("/api/mobile/geofences?city={$city->slug}&lat={$lat}&lng=${lng}", $this->headers($user))->getData();
		$this->assertCount(2, $response->data);
	}

	function test_an_unauthorized_user_in_geofence_is_not_stored_in_the_database() {
		$this->withExceptionHandling();
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');

		$profile = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$account = create('App\Account', ['profile_id' => $profile->id]);
		$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id]);

		$data = [
			'current' => [
				['location_id' => $profile->id, 'action' => 'enter']
			],
			'remove' => []
		];

		$this->json('POST', '/api/mobile/geofences', $data)->assertStatus(401);
	}

	function test_an_authorized_user_in_geofence_is_stored_in_the_database() {
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');

		$profile = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$account = create('App\Account', ['profile_id' => $profile->id]);
		$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id]);

		$data = [
			'current' => [
				['location_id' => $profile->id, 'action' => 'enter']
			],
			'remove' => []
		];

		$this->json('POST', '/api/mobile/geofences', $data, $this->headers($user));
		$this->assertDatabaseHas('user_locations', ['user_id' => $user->id, 'profile_id' => $profile->id]);
	}

	function test_a_user_entering_geofence_creates_a_customer_break_geofence_event() {
		Notification::fake();
		$this->expectsEvents(CustomerBreakGeoFence::class);
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');

		$profile = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$account = create('App\Account', ['profile_id' => $profile->id]);
		$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id]);

		$data = [
			'current' => [
				['location_id' => $profile->id, 'action' => 'enter']
			],
			'remove' => []
		];

		$this->json('POST', '/api/mobile/geofences', $data, $this->headers($user));
	}

	function test_a_user_geofence_is_sent_a_pockeyt_pay_notification() {
		Notification::fake();
		$user = create('App\User');
		$pushToken = create('App\PushToken', ['user_id' => $user->id]);
		$logo = create('App\Photo');
		$city = create('App\City');

		$profile = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$account = create('App\Account', ['profile_id' => $profile->id]);
		$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id]);

		$data = [
			'current' => [
				['location_id' => $profile->id, 'action' => 'enter']
			],
			'remove' => []
		];

		$this->json('POST', '/api/mobile/geofences', $data, $this->headers($user));

		Notification::assertSentTo(
      [$user],
      CustomerEnterGeoFence::class
    );
	}
}
