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

		$response = $this->get("/api/mobile/v1/profiles?city={$city->slug}&query=random")->getData();
		$this->assertEquals(0, count($response->data));
	}

	function test_a_mobile_user_can_filter_by_google_rating() {
		$city = create('App\City');
		$photo = create('App\Photo');

		$profile1 = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true, 'google_rating' => 5]);
		$profile2 = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true, 'google_rating' => 4]);
		$profile3 = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true, 'google_rating' => 3]);
		$profile4 = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true, 'google_rating' => 2]);
		$profile5 = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true, 'google_rating' => 1]);

		$response = $this->get("/api/mobile/v1/profiles?city={$city->slug}&rating=1")->getData();

		$this->assertEquals(5, $response->data[0]->google_rating);
		$this->assertEquals(4, $response->data[1]->google_rating);
		$this->assertEquals(3, $response->data[2]->google_rating);
		$this->assertEquals(2, $response->data[3]->google_rating);
		$this->assertEquals(1, $response->data[4]->google_rating);
	}

	function test_a_mobile_user_can_filter_by_tags() {
		$city = create('App\City');
		$photo = create('App\Photo');

		$profile1 = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true]);
		$tag1 = create('App\Tag');
		$profile1->tags()->sync($tag1);

		$profile2 = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true]);
		$tag2 = create('App\Tag');
		$profile2->tags()->sync($tag2);

		$profile3 = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'approved' => true]);
		$profile3->tags()->sync($tag1);

		$response = $this->get("/api/mobile/v1/profiles?city={$city->slug}&tags[]={$tag1->id}")->getData();
		$this->assertEquals(2, count($response->data));
		$this->assertEquals($tag1->id, $response->data[0]->tags[0]->id);
		$this->assertEquals($tag1->id, $response->data[1]->tags[0]->id);
	}
}
