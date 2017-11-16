<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiEventTest extends TestCase
{
	use RefreshDatabase;

	function test_a_mobile_user_can_retrieve_events_today() {
		$city = create('App\City');
		$photo = create('App\Photo');
		$profile = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
		$events = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::now()], 3);
		$eventsNotToday = create('App\Post', ['profile_id' => $profile->id, 'event_date' => (Carbon::now())->addDays(2)], 2);

		$response = $this->get("/api/mobile/v1/posts?city={$city->slug}&event=today")->getData();
		$this->assertCount(3, $response->data);
	}

	function test_a_mobile_user_can_retrieve_events_tomorrow() {
		$city = create('App\City');
		$photo = create('App\Photo');
		$profile = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
		$events = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::tomorrow()], 3);
		$eventsNotTomorrow = create('App\Post', ['profile_id' => $profile->id, 'event_date' => (Carbon::now())->addDays(2)], 2);

		$response = $this->get("/api/mobile/v1/posts?city={$city->slug}&event=tomorrow")->getData();
		$this->assertCount(3, $response->data);
	}

	function test_a_mobile_user_can_retrieve_events_week() {
		$city = create('App\City');
		$photo = create('App\Photo');
		$profile = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);


		$eventNotInWeek = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::now()->startOfWeek()->subDay()], 1);
		$eventMonday = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::now()->startOfWeek()], 1);
		$eventWednesday = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::now()->startOfWeek()->addDays(2)], 1);
		$eventFriday = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::now()->startOfWeek()->addDays(4)], 1);
		$eventSaturday = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::now()->startOfWeek()->addDays(5)], 1);
		$eventNotInWeek = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::now()->startOfWeek()->addDays(7)], 1);
		
		$response = $this->get("/api/mobile/v1/posts?city={$city->slug}&event=week")->getData();
		$this->assertCount(4, $response->data);
	}

	function test_a_mobile_user_can_retrieve_events_weeekend() {
		$city = create('App\City');
		$photo = create('App\Photo');
		$profile = create('App\Profile', ['city_id' => $city->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);


		$eventFriday = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::now()->startOfWeek()->addDays(4)], 1);
		$eventSaturday = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::now()->startOfWeek()->addDays(5)], 1);
		$eventSunday = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::now()->startOfWeek()->addDays(6)], 1);

		$eventMonday = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::now()->startOfWeek()], 1);
		$eventWednesday = create('App\Post', ['profile_id' => $profile->id, 'event_date' => Carbon::now()->startOfWeek()->addDays(2)], 1);

		$response = $this->get("/api/mobile/v1/posts?city={$city->slug}&event=weekend")->getData();
		$this->assertCount(3, $response->data);
	}
}
