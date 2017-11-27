<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DealTest extends TestCase
{
  use RefreshDatabase;

  function test_an_unauthorized_user_cannot_view_deal_index() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');

		$this->get("/deals/" . $profile->slug)->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/deals/" . $profile->slug)->assertStatus(403);
	}

	function test_an_authorized_user_can_view_deal_index() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);

  	$response = $this->get("/deals/" . $profile->slug)->assertSee('Add | Active Deals');
	}

	function test_an_unauthorized_user_cannot_create_a_deal() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$deal = make('App\Post', [
			'message' => "Fake Test Deal",
			'deal_item' => 'Coffee',
			'price' => '$ 10.00',
			'end_date' => date("Y-m-d"),
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		]);

		$this->post("/deals/" . $profile->slug, $deal->toArray())->assertRedirect('/login');
  	$this->signIn();
  	$this->post("/deals/" . $profile->slug, $deal->toArray())->assertStatus(403);
	}

	function test_an_authorized_user_can_create_a_deal() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		Storage::fake('public');

		$data = [
			'message' => "Fake Test Deal",
			'deal_item' => 'Coffee',
			'price' => '$ 10.00',
			'end_date' => date("Y-m-d"),
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		];

		$this->json('POST', "deals/{$profile->slug}", $data);
		$deal = Post::first();

		$this->assertDatabaseHas('posts', ['profile_id' => $profile->id, 'deal_item' => 'Coffee']);
		$this->assertEquals('images/photos/' . $file->hashName(), $deal->photo->path);
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


		$data = [
			'message' => "Fake Test Deal",
			'deal_item' => 'Coffee',
			'price' => '$ 10.00',
			'end_date' => date("Y-m-d"),
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		];

		$this->json('POST', "deals/{$profile->slug}", $data);

		$deal = Post::first();
		$this->get("/deals/" . $profile->slug . '/' . $deal->id)->assertSee("Fake Test Deal");
	}

	function test_an_unathorized_user_cannot_delete_a_deal() {
		$this->withExceptionHandling();
		$deal = create('App\Post');

		$this->delete("/deals/" . $deal->id)->assertRedirect('/login');
		$this->signIn();
		$this->delete("/deals/" . $deal->id)->assertStatus(403);
	}

	function test_an_authorized_user_can_delete_a_deal() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		Storage::fake('public');

		$data = [
			'message' => "Fake Test Deal",
			'deal_item' => 'Coffee',
			'price' => '$ 10.00',
			'end_date' => date("Y-m-d"),
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		];

		$this->json('POST', "deals/{$profile->slug}", $data);
		$deal = Post::first();
		$this->delete("/deals/" . $deal->id);

		$this->assertDatabaseMissing('posts', ['id' => $deal->id]);
		$this->assertDatabaseMissing('photos', ['name' => $deal->photo_name]);
		Storage::disk('public')->assertMissing('public/images/photos/' . $file->hashName());
	}

	function test_unauthorized_users_cannot_retrieve_analytics_data_on_deals() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$deal = create('App\Post', ['profile_id' => $profile->id]);

		$this->json('GET', "api/web/deals/{$deal->id}")->assertStatus(401);
		$this->signIn();
		$this->json('GET', "api/web/deals/{$deal->id}")->assertStatus(403);
	}
}
