<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiAuthenticateUsersTest extends TestCase
{
	use RefreshDatabase;

	function test_a_user_can_register_via_api_and_is_returned_a_token() {
		$data = [
			'first_name' => 'Test',
			'last_name' => 'User',
			'email' => 'test@pockeyt.com',
			'password' => 'TestCase1!',
			'password_confirmation' => 'TestCase1!'
		];

  	$response = $this->post("/api/mobile/auth/register", $data)->getData();
  	$this->assertDatabaseHas('users', ['first_name' => 'Test']);
  	$this->assertNotEmpty($response->user->token);
	}

	function test_an_unauthorized_user_cannot_login_via_api() {
		create('App\User', ['email' => 'test@pockeyt.com', 'password' => Hash::make('TestCase1!')]);

		$data = [
			'email' => 'another@pockeyt.com',
			'password' => 'UserTest1!'
		];
		$this->post("/api/mobile/auth/login", $data)->assertStatus(422);
	}

	function test_an_authorized_user_can_login_via_api() {
		create('App\User', ['email' => 'test@pockeyt.com', 'password' => Hash::make('TestCase1!')]);

		$data = [
			'email' => 'test@pockeyt.com',
			'password' => 'TestCase1!'
		];
		$response = $this->post("/api/mobile/auth/login", $data)->getData();
		$this->assertNotEmpty($response->user->token);
	}

	function test_an_authenticated_user_can_get_their_user_data() {
		$user = create('App\User');
		$response = $this->get("/api/mobile/auth/me", $this->headers($user))->getData();
		$this->assertEquals($user->first_name, $response->data->first_name);
		$this->assertEquals($user->last_name, $response->data->last_name);
	}

	function test_an_unauthenticated_user_cannot_get_their_data() {
		$user = create('App\User');
		$response = $this->get("/api/mobile/auth/me")->getData();
		$this->assertEquals('token_absent', $response->error);
	}

	function test_a_user_whose_token_is_invalid_cannot_get_their_data() {
		$user = create('App\User');
		$headers = $this->headers($user);
		$headers['Authorization'] = str_replace_first(substr($headers['Authorization'], -1), '', $headers['Authorization']);
		$response = $this->get("/api/mobile/auth/me", $headers)->getData();
		$this->assertEquals('token_invalid', $response->error);
	}
}
