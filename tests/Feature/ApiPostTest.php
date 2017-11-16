<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiPostTest extends TestCase
{
	use RefreshDatabase;

	function test_a_mobile_user_can_retrieve_all_posts_for_location() {
		$city = create('App\City');

		$photo = create('App\Photo');
		$profile = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
		$posts = create('App\Post', ['profile_id' => $profile->id], 10);

		$profileTwo = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
		$posts = create('App\Post', ['profile_id' => $profileTwo->id], 10);

  	$response = $this->get("/api/mobile/v1/posts?city={$city->slug}")->getData();
  	$this->assertCount(10, $response->data);
  	$this->assertEquals('http://pockeyt.dev/api/mobile/v1/posts?city=raleigh&page=2', $response->links->next);
	}

	function test_a_mobile_user_retrieves_posts_only_from_their_location() {
		$city = create('App\City');
		$otherCity = create('App\City', ['name' => 'Chapel Hill', 'county' => 'Orange', 'state' => 'NC']);

		$photo = create('App\Photo');
		$profile = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
		$posts = create('App\Post', ['profile_id' => $profile->id], 5);

		$profileTwo = create('App\Profile', ['city_id' => $otherCity->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
		$posts = create('App\Post', ['profile_id' => $profileTwo->id], 10);

  	$response = $this->get("/api/mobile/v1/posts?city={$city->slug}")->getData();
  	$this->assertCount(5, $response->data);
	}

	function test_only_deals_whose_end_date_has_not_passed_are_fetched_with_posts() {
		$city = create('App\City');
		$photo = create('App\Photo');
		$profile = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
		$posts = create('App\Post', ['profile_id' => $profile->id], 2);
		$dealExpired = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'end_date' => (Carbon::now())->subDay()]);
		$dealCurrent = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'end_date' => (Carbon::now())->addDay()]);

		$response = $this->get("/api/mobile/v1/posts?city={$city->slug}")->getData();
  	$this->assertCount(3, $response->data);
	}

	function test_a_mobile_user_can_retrieve_their_favorited_profiles_posts() {
		$city = create('App\City');
		$photo = create('App\Photo');
		$profileFav1 = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
		$postsFav1 = create('App\Post', ['profile_id' => $profileFav1->id], 3);

		$profileFav2 = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
		$postsFav2 = create('App\Post', ['profile_id' => $profileFav2->id], 3);

		$profileNotFav = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
		$postsNotFav = create('App\Post', ['profile_id' => $profileNotFav->id], 3);

		$response = $this->get("/api/mobile/v1/posts?city={$city->slug}&favs[]={$profileFav1->slug}&favs[]={$profileFav2->slug}")->getData();
		$this->assertCount(6, $response->data);
	}

	function test_a_mobile_user_can_retrieve_their_bookmarked_posts(){
		$photo = create('App\Photo');

		$profileOne = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
		$bookmarkedPostOne = create('App\Post', ['profile_id' => $profileOne->id]);

		$profileTwo = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
		$bookmarkedPostTwo = create('App\Post', ['profile_id' => $profileTwo->id]);
		$notBookmarkedPost = create('App\Post', ['profile_id' => $profileTwo->id]);

		$response = $this->get("/api/mobile/v1/posts?bookmarks[]={$bookmarkedPostOne->id}&bookmarks[]={$bookmarkedPostTwo->id}")->getData();
		$this->assertCount(2, $response->data);
	}
}
