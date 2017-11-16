<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_view_a_user_account() {
    $this->withExceptionHandling();
    $user = create('App\User');
    $this->get("/users/{$user->id}")->assertRedirect('/login');

    $unAuthUser = create('App\User');
    $this->actingAs($unAuthUser)->get("/users/{$user->id}")->assertStatus(403);
  }

  function test_an_authorized_user_can_view_their_user_account() {
  	$this->signIn();
  	$user = create('App\User');
    $profile = create('App\Profile', ['user_id' => $user->id]);

    $this->actingAs($user)->get("/users/{$user->id}")
    	->assertSee($user->first_name)
    	->assertSee($user->last_name)
    	->assertSee('User Profile');
  }

  function test_an_unauthorized_user_cannot_update_a_user_account() {
  	$this->withExceptionHandling();
  	$user = create('App\User');

  	$data = [
  		'first_name' => 'test',
  		'last_name' => 'last',
  		'email' => 'test@pockeyt.com'
  	];

  	$this->patch('/users/{$user->id}', $data)->assertRedirect('/login');

  	$unAuthUser = create('App\User');
    $this->actingAs($unAuthUser)->patch("/users/{$user->id}", $data)->assertStatus(403);
  }

  function test_an_authorized_user_can_update_their_names_email() {
  	$this->signIn();
  	$user = create('App\User');

  	$first = 'test';
  	$last = 'last';
  	$email = 'test@pockeyt.com';
  	$data = [
  		'first_name' => $first,
  		'last_name' => $last,
  		'email' => $email
  	];

  	$this->actingAs($user)->patch("/users/{$user->id}", $data)
  		->assertRedirect("/users/{$user->id}");
  	$this->assertDatabaseHas('users', ['first_name' => $first, 'last_name' => $last, 'email' => $email]);
  }

  function test_an_authorized_user_can_update_their_password() {
  	$this->signIn();
  	$oldPassword = 'passw0rd1!!';
		$newPassword = 'newP@55W0rd1!!';
  	$user = create('App\User', ['password' => Hash::make($oldPassword)]);

  	$data = [
			'old_password' => $oldPassword,
			'password'=> $newPassword,
			'password_confirmation' => $newPassword
		];

  	$this->actingAs($user)->patch("/users/{$user->id}", $data)
  		->assertRedirect("/users/{$user->id}");

  	$dbUser = User::find($user->id);
  	$this->assertTrue(Hash::check($newPassword, $dbUser->password));
  }

  function test_an_authorized_user_cannot_update_their_password_if_incorrect_password() {
  	$this->withExceptionHandling();
  	$this->signIn();
  	$oldPassword = 'passw0rd1!!';
		$newPassword = 'newP@55W0rd1!!';
  	$user = create('App\User', ['password' => Hash::make($oldPassword)]);

  	$data = [
			'old_password' => $oldPassword . 'wrong',
			'password'=> $newPassword,
			'password_confirmation' => $newPassword
		];

  	$this->actingAs($user)->patch("/users/{$user->id}", $data);

  	$dbUser = User::find($user->id);
  	$this->assertNotTrue(Hash::check($newPassword, $dbUser->password));
  }

  function test_an_authorized_user_can_add_photo_no_previous_photo() {
		Storage::fake('public');
		$user = create('App\User');

		$data = [
			'photo' => $file = UploadedFile::fake()->image('photo.jpg')
		];
		$this->actingAs($user)->patch("/users/{$user->id}", $data)
			->assertRedirect("/users/{$user->id}");
		$this->assertEquals('images/photos/' . $file->hashName(), $user->fresh()->photo->path);
		Storage::disk('public')->assertExists('images/photos/' . $file->hashName());
	}

	function test_an_authorized_user_can_add_photo_with_previous_photo() {
		Storage::fake('public');
		$user = create('App\User');

		$data = [
			'photo' => $file = UploadedFile::fake()->image('photo.jpg')
		];
		$this->actingAs($user)->patch("/users/{$user->id}", $data);
		$this->assertEquals('images/photos/' . $file->hashName(), $user->fresh()->photo->path);
		Storage::disk('public')->assertExists('images/photos/' . $file->hashName());
		
		$oldPhotoId = $user->fresh()->photo->id;

		$newData = [
			'photo' => $newFile = UploadedFile::fake()->image('new_photo.jpg')
		];
		$this->actingAs($user)->patch("/users/{$user->id}", $newData)
			->assertRedirect("/users/{$user->id}");
		$this->assertEquals('images/photos/' . $newFile->hashName(), $user->fresh()->photo->path);
		Storage::disk('public')->assertExists('images/photos/' . $newFile->hashName());

		$this->assertDatabaseMissing('photos', ['id' => $oldPhotoId]);
		Storage::disk('public')->assertMissing('public/images/photos/' . $file->hashName());
	}
}
