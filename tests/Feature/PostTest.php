<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Post;
use App\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_view_post_index() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');

		$this->get("/posts/" . $profile->slug)->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/posts/" . $profile->slug)->assertStatus(403);
	}

	function test_an_authorized_user_can_view_post_index() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);

  	$response = $this->get("/posts/" . $profile->slug)->assertSee('Add | Recent Posts');
	}

	function test_an_unauthorized_user_cannot_create_a_post() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$post = make('App\Post');

		$this->post("/posts/" . $profile->slug, $post->toArray())->assertRedirect('/login');
  	$this->signIn();
  	$this->post("/posts/" . $profile->slug, $post->toArray())->assertStatus(403);
	}

	function test_an_authorized_user_can_create_a_post() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);

		$data = [
			'message' => "Fake Test Post"
		];

		$response = $this->post("/posts/" . $profile->slug, $data);
		$this->get($response->headers->get('Location'))
      ->assertSee('Fake Test Post');
    $this->assertDatabaseHas('posts', ['profile_id' => $profile->id, 'message' => 'Fake Test Post']);
	}

	function test_an_authorized_user_can_create_a_post_with_a_picture() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		Storage::fake('public');

		$data = [
			'message' => "Fake Test Post With Image",
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		];

		$this->json('POST', "posts/{$profile->slug}", $data);
		$post = Post::first();

		$this->assertDatabaseHas('posts', ['profile_id' => $profile->id, 'message' => 'Fake Test Post With Image']);
		$this->assertEquals('images/photos/' . $file->hashName(), $post->photo->path);
  	Storage::disk('public')->assertExists('images/photos/' . $file->hashName());
	}

	function test_any_user_can_see_a_profiles_post() {
		$this->signIn();
    Storage::fake('public');

  	$profile = create('App\Profile', ['user_id' => auth()->id()]);

  	$data = [
  		'type' => 'logo',
  		'photo' => $file = UploadedFile::fake()->image('logo.jpg')
  	];

  	$this->json('POST', "photos/{$profile->slug}", $data);
  	$profile = Profile::first();
		$post = create('App\Post', ['profile_id' => $profile->id]);

		$this->get("/posts/" . $profile->slug . '/' . $post->id)->assertSee($post->message);
	}

	function test_an_unathorized_user_cannot_delete_a_post() {
		$this->withExceptionHandling();
		$post = create('App\Post');

		$this->delete("/posts/" . $post->id)->assertRedirect('/login');
		$this->signIn();
		$this->delete("/posts/" . $post->id)->assertStatus(403);
	}

	function test_an_authorized_user_can_delete_a_post() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		$post = create('App\Post', ['profile_id' => $profile->id]);

		$this->delete("/posts/" . $post->id);

		$this->assertDatabaseMissing('posts', ['id' => $post->id]);
	}

	function test_an_authorized_user_can_delete_a_post_with_a_photo() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		Storage::fake('public');

		$data = [
			'message' => "Fake Test Post With Image",
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		];

		$this->json('POST', "posts/{$profile->slug}", $data);
		$post = Post::first();
		$this->delete("/posts/" . $post->id);

		$this->assertDatabaseMissing('posts', ['id' => $post->id]);
		$this->assertDatabaseMissing('photos', ['name' => $post->photo_name]);
		Storage::disk('public')->assertMissing('public/images/photos/' . $file->hashName());
	}
}
