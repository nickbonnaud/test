<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiProfileTest extends TestCase
{
	use RefreshDatabase;

	function test_a_mobile_user_can_retrieve_all_profiles_for_location() {
		$city = create('App\City');

		$photo = create('App\Photo');
		$profiles = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true], 19);

  	$response = $this->get("/api/mobile/v1/profiles?city={$city->slug}")->getData();
  	$this->assertCount(10, $response->data);
  	$this->assertEquals('http://pockeyt.dev/api/mobile/v1/profiles?city=raleigh&page=2', $response->links->next);
	}

	function test_a_mobile_user_can_retrieve_all_profiles_only_for_current_location() {
		$city = create('App\City');
		$newCity = create('App\City');

		$photo = create('App\Photo');
		$profiles = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true], 5);

		$profileNotCity = create('App\Profile', ['city_id' => $newCity->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true], 5);

  	$response = $this->get("/api/mobile/v1/profiles?city={$city->slug}")->getData();
  	$this->assertCount(5, $response->data);
	}

	function test_a_mobile_user_can_retrieve_all_profiles_for_location_that_are_approved() {
		$city = create('App\City');

		$photo = create('App\Photo');
		$profiles = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true], 5);

		$profileNotCity = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => false], 5);

  	$response = $this->get("/api/mobile/v1/profiles?city={$city->slug}")->getData();
  	$this->assertCount(5, $response->data);
	}

	function test_a_mobile_user_can_search_businesses_by_name() {
		$city = create('App\City');

		$photo = create('App\Photo');
		$profile = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true]);

		$profileNotSearch = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => false], 5);

		$businessName = $profile->business_name;

		$response = $this->get("/api/mobile/v1/profiles?city={$city->slug}&query=" . $businessName)->getData();
		$this->assertEquals($profile->id, $response->data[0]->id);

		
		$businessName = substr($businessName, 2);
		$response = $this->get("/api/mobile/v1/profiles?city={$city->slug}&query=" . $businessName)->getData();
		$this->assertEquals($profile->id, $response->data[0]->id);

		$businessName = substr($businessName, 0, -2);
		$response = $this->get("/api/mobile/v1/profiles?city={$city->slug}&query=" . $businessName)->getData();
		$this->assertEquals($profile->id, $response->data[0]->id);
	}
}
