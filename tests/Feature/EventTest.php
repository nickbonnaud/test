<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Post;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventTest extends TestCase
{
  use RefreshDatabase;

  function test_an_unauthorized_user_cannot_view_event_index() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');

		$this->get("/events/" . $profile->slug)->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/events/" . $profile->slug)->assertStatus(403);
	}

	function test_an_authorized_user_can_view_post_index() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);

  	$response = $this->get("/events/" . $profile->slug)->assertSee('Add | Recent Events');
	}

	function test_an_unauthorized_user_cannot_create_an_event() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$post = make('App\Post', [
			'title' => "Fake Test Event",
			'body' => "Fake Test Event Body",
			'event_date' => date("Y-m-d"),
			'event_time' => '4:00pm',
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		]);

		$this->post("/events/" . $profile->slug, $post->toArray())->assertRedirect('/login');
  	$this->signIn();
  	$this->post("/events/" . $profile->slug, $post->toArray())->assertStatus(403);
	}

	function test_an_authorized_user_can_create_an_event() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		Storage::fake('public');
		
		$date =  date("Y-m-d");
		$time = '4:00pm';
		$data = [
			'title' => "Fake Test Event",
			'body' => "Fake Test Event Body",
			'event_date' => $date,
			'event_time' => $time,
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		];

		$this->json('POST', "events/{$profile->slug}", $data);
		$event = Post::first();
		$this->assertDatabaseHas('posts', ['profile_id' => $profile->id, 'event_date' => new Carbon($date . ' ' . $time)]);
		$this->assertEquals('images/photos/' . $file->hashName(), $event->photo->path);
  	Storage::disk('public')->assertExists('images/photos/' . $file->hashName());
	}

	function test_any_user_can_see_a_profiles_event() {
		$this->signIn();
    Storage::fake('public');
  	$profile = create('App\Profile', ['user_id' => auth()->id()]);

  	$data = [
  		'type' => 'logo',
  		'photo' => $file = UploadedFile::fake()->image('logo.jpg')
  	];
  	$this->json('POST', "photos/{$profile->slug}", $data);

  	$date =  date("Y-m-d");
		$time = '4:00pm';
		$data = [
			'title' => "Fake Test Event",
			'body' => "Fake Test Event Body",
			'event_date' => $date,
			'event_time' => $time,
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		];

		$this->json('POST', "events/{$profile->slug}", $data);

		$event = Post::first();
		$this->get("/events/" . $profile->slug . '/' . $event->id)->assertSee("Fake Test Event");
	}

	function test_an_unathorized_user_cannot_delete_an_event() {
		$this->withExceptionHandling();
		$event = create('App\Post');

		$this->delete("/events/" . $event->id)->assertRedirect('/login');
		$this->signIn();
		$this->delete("/events/" . $event->id)->assertStatus(403);
	}

	function test_an_authorized_user_can_delete_an_event() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		Storage::fake('public');

		
		$date =  date("Y-m-d");
		$time = '4:00pm';
		$data = [
			'title' => "Fake Test Event",
			'body' => "Fake Test Event Body",
			'event_date' => $date,
			'event_time' => $time,
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		];

		$this->json('POST', "events/{$profile->slug}", $data);
		$event = Post::first();
		$this->delete("/events/" . $event->id);

		$this->assertDatabaseMissing('posts', ['id' => $event->id]);
		$this->assertDatabaseMissing('photos', ['name' => $event->photo_name]);
		Storage::disk('public')->assertMissing('public/images/photos/' . $file->hashName());
	}
}
