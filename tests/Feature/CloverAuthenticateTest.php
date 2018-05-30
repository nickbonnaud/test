<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class CloverAuthenticateTest extends TestCase
{
  use RefreshDatabase;

  function test_a_clover_client_can_login() {
  	$user = create('App\User', ['email' => 'test@pockeyt.com', 'password' => Hash::make('TestCase1!')]);
  	$profile = create('App\Profile', ['user_id' => $user->id]);

  	$data = [
  		'email' => 'test@pockeyt.com',
  		'password' => 'TestCase1!'
  	];
  	$response = $this->post("/api/mobile/pay/login", $data)->getData();
  	dd($response);
  }

  function test_a_clover_client_can_retrieve_their_data() {

  	$profile = create('App\Profile');
  	$account = create('App\Account', ['profile_id' => $profile->id]);

  	$response = $this->get("/api/mobile/pay/me")->getData();
  	$this->assertCount(2, $response->data);
  }
}
