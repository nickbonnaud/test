<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiUserTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_update_user_info() {
		$this->withExceptionHandling();
  	$user = create('App\User');

  	$firstName = "Test";
  	$lastName = "Last";

  	$data = [
  		'first_name' => $firstName,
  		'last_name' => $lastName
  	];

  	$this->json("PATCH", "/api/mobile/user", $data)->assertStatus(401);
	}

	function test_an_authorized_user_can_update_last_first_name() {
  	$user = create('App\User');

  	$firstName = "Test";
  	$lastName = "Last";

  	$data = [
  		'first_name' => $firstName,
  		'last_name' => $lastName,
  		'email' => $user->email
  	];

  	$response = $this->json("PATCH", "/api/mobile/user", $data, $this->headers($user))->getData();
  	$this->assertEquals($firstName, $response->data->first_name);
  	$this->assertEquals($lastName, $response->data->last_name);
  	$this->assertNotNull($response->data->token);
	}


	function test_an_authorized_user_can_update_email() {
  	$user = create('App\User');

  	$email = "test@pockeyt.com";

  	$data = [
  		'first_name' => $user->first_name,
  		'last_name' => $user->last_name,
  		'email' => $email
  	];

  	$response = $this->json("PATCH", "/api/mobile/user", $data, $this->headers($user))->getData();
  	$this->assertEquals($email, $response->data->email);
  	$this->assertNotNull($response->data->token);
	}

	function test_an_authorized_user_can_update_their_password() {
		$oldPassword = 'passw0rd1!';
		$newPassword = 'newP@55W0rd1!';
		$user = create('App\User', ['password' => Hash::make($oldPassword)]);

		$data = [
			'old_password' => $oldPassword,
			'password'=> $newPassword,
			'password_confirmation' => $newPassword
		];

		$response = $this->json("PATCH", "/api/mobile/user", $data, $this->headers($user))->getData();
		$dbUser = User::find($user->id);
		$this->assertTrue(Hash::check($newPassword, $dbUser->password));
		$this->assertNotNull($response->data->token);
	}

	function test_an_authorized_user_cannot_update_their_password_if_wrong() {
		$this->withExceptionHandling();
		$oldPassword = 'passw0rd1!';
		$newPassword = 'newP@55W0rd1!';
		$user = create('App\User', ['password' => Hash::make($oldPassword)]);

		$data = [
			'old_password' => $oldPassword . 'wrong',
			'password'=> $newPassword,
			'password_confirmation' => $newPassword
		];

		$response = $this->json("PATCH", "/api/mobile/user", $data, $this->headers($user))->getData();
		$dbUser = User::find($user->id);
		$this->assertNotTrue(Hash::check($newPassword, $dbUser->password));
		$this->assertEquals("The given data was invalid.", $response->message);
	}

	function test_an_authorized_user_can_add_photo_no_previous_photo() {
		Storage::fake('public');
		$user = create('App\User');

		$data = [
			'photo' => $file = UploadedFile::fake()->image('photo.jpg')
		];
		$response = $this->json('PATCH', "/api/mobile/user", $data, $this->headers($user))->getData();

		$filePath = 'images/photos/' . $file->hashName();
		$userFilePath = $user->fresh()->photo->path;
		
		$this->assertEquals($filePath, $userFilePath);
		Storage::disk('public')->assertExists('images/photos/' . $file->hashName());
		$this->assertNotNull($response->data->photo_url);
	}

	function test_an_authorized_user_can_add_photo_with_previous_photo() {
		Storage::fake('public');
		$user = create('App\User');

		$data = [
			'photo' => $file = UploadedFile::fake()->image('photo.jpg')
		];
		$this->json('PATCH', "/api/mobile/user", $data, $this->headers($user));
		$this->assertEquals('images/photos/' . $file->hashName(), $user->fresh()->photo->path);
		Storage::disk('public')->assertExists('images/photos/' . $file->hashName());
		
		$oldPhotoId = $user->fresh()->photo->id;

		$newData = [
			'photo' => $newFile = UploadedFile::fake()->image('new_photo.jpg')
		];
		$this->json('PATCH', "/api/mobile/user", $newData, $this->headers($user));
		$this->assertEquals('images/photos/' . $newFile->hashName(), $user->fresh()->photo->path);
		Storage::disk('public')->assertExists('images/photos/' . $newFile->hashName());

		$this->assertDatabaseMissing('photos', ['id' => $oldPhotoId]);
		Storage::disk('public')->assertMissing('public/images/photos/' . $file->hashName());
	}

	function test_a_new_user_can_check_if_email_is_taken() {
		$user = create('App\User');
		$response = $this->get("/api/mobile/user?email={$user->email}&unique=true")->getData();
		$this->assertFalse($response->unique);

		$newEmail = "new@email.com";
		$response = $this->get("/api/mobile/user?email={$newEmail}&unique=true")->getData();
		$this->assertTrue($response->unique);
	}
}
