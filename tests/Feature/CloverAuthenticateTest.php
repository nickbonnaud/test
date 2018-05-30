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
			'password' => 'UserTest1!'
		];
		$response = $this->post("/api/mobile/pay/login", $data)->getData();
		$this->assertEquals("invalid_email_or_password", $response->error);
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

  function test_a_clover_client_can_retrieve_their_data() {
  	$photo = create('App\Photo');
  	$profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);

  	$response = $this->get("/api/mobile/pay/me")->getData();
  	dd($response);
  	$this->assertCount(2, $response->data);
  }
}
