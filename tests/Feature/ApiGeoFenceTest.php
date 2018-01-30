<?php

namespace Tests\Feature;

use Tests\TestCase;
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
		
		$lat = $geoLocationIn->latitude;
		$lng = $geoLocationIn->longitude;

		$response = $this->get("/api/mobile/geofences?city={$city->slug}&lat={$lat}&lng=${lng}", $this->headers($user))->getData();
		$this->assertCount(1, $response->data);
	}

	function test_an_authorized_user_can_send_active_and_exited_geofences() {
		$user = create('App\User');
		$logo = create('App\Photo');
		$city = create('App\City');

		$profileActive = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$geoLocationIn = create('App\GeoLocation', ['profile_id' => $profileActive->id]);

		$profileNotActive = create('App\Profile', ['logo_photo_id' => $logo->id, 'city_id' => $city->id]);
		$geoLocationOut = create('App\GeoLocation', ['profile_id' => $profileNotActive->id]);
	}
}
