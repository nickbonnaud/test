<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class CloverCustomersTest extends TestCase
{
  use RefreshDatabase;

  function test_an_authorized_clover_client_can_retrieve_customers() {
  	Notification::fake();
    $user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);
  	$photo = create('App\Photo');
  	$user1 = create('App\User', ["photo_id" => $photo->id]);
  	$user2 = create('App\User', ["photo_id" => $photo->id]);

  	create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user1->id]);
  	create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user2->id]);

  	$response = $this->get("/api/mobile/pay/customers", $this->headers($user))->getData();
  	$this->assertCount(2, $response->data);
  }

  function test_an_unauthorized_clover_client_cannot_retrieve_customers() {
    $this->withExceptionHandling();
    Notification::fake();
    $user = create('App\User');
    $profile = create('App\Profile', ['user_id' => $user->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);
    $photo = create('App\Photo');
    $user1 = create('App\User', ["photo_id" => $photo->id]);
    $user2 = create('App\User', ["photo_id" => $photo->id]);

    create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user1->id]);
    create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user2->id]);

    $response = $this->get("/api/mobile/pay/customers")->assertStatus(401);
  }
}
