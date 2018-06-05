<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class CloverAuthenticateTest extends TestCase
{
  use RefreshDatabase;

  function test_an_unauthorized_clover_client_cannot_login() {
  	$user = create('App\User', ['email' => 'test@pockeyt.com', 'password' => Hash::make('TestCase1!')]);
  	$photo = create('App\Photo');
  	$profile = create('App\Profile', ['user_id' => $user->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);

  	$data = [
			'email' => 'another@pockeyt.com',
			'password' => 'TestCase1!'
		];
		$response = $this->post("/api/mobile/pay/login", $data)->getData();
		$this->assertEquals("invalid_email", $response->error);

    $data = [
      'email' => 'test@pockeyt.com',
      'password' => 'TestCaseBad!'
    ];
    $response = $this->post("/api/mobile/pay/login", $data)->getData();
    $this->assertEquals("invalid_password", $response->error);

		$response = $this->post("/api/mobile/pay/login", $data)->assertStatus(422);
  }

  function test_an_authorized_clover_client_can_login() {
  	$user = create('App\User', ['email' => 'test@pockeyt.com', 'password' => Hash::make('TestCase1!')]);
  	$photo = create('App\Photo');
  	$profile = create('App\Profile', ['user_id' => $user->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);

  	$data = [
  		'email' => 'test@pockeyt.com',
  		'password' => 'TestCase1!'
  	];
  	$response = $this->post("/api/mobile/pay/login", $data)->getData();
  	$this->assertNotEmpty($response->data->token);
  }

  function test_an_authenticated_clover_client_can_retrieve_their_data() {
  	$user = create('App\User');
  	$photo = create('App\Photo');
  	$profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'user_id' => $user->id]);

  	$response = $this->get("/api/mobile/pay/me", $this->headers($user))->getData();
  	$this->assertEquals($profile->business_name, $response->data->business_name);
  	$this->assertNotEmpty($response->data->token);
  }

  function test_an_unathorized_user_cannot_get_data() {
  	$user = create('App\User');
  	$photo = create('App\Photo');
  	$profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'user_id' => $user->id]);

  	$response = $this->get("/api/mobile/pay/me")->getData();
  	$this->assertEquals('token_absent', $response->error);
  }

  function test_a_user_whose_token_is_invalid_cannot_get_data() {
  	$user = create('App\User');
  	$photo = create('App\Photo');
  	$profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id, 'user_id' => $user->id]);

  	$headers = $this->headers($user);
  	$headers['Authorization'] = str_replace_first(substr($headers['Authorization'], -1), '', $headers['Authorization']);
  	$response = $this->get("/api/mobile/pay/me", $headers)->getData();
  	$this->assertEquals('token_invalid', $response->error);
  }
}
