<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class CloverCustomersTest extends TestCase
{
  use RefreshDatabase;

  function test_a_clover_client_can_retrieve_customers() {
  	Notification::fake();

  	$profile = create('App\Profile');
  	$account = create('App\Account', ['profile_id' => $profile->id]);
  	$user1 = create('App\User');
  	$user2 = create('App\User');

  	create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user1->id]);
  	create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user2->id]);

  	$response = $this->get("/api/mobile/pay/customers")->getData();
  	dd($response);
  	$this->assertCount(2, $response->data);
  }
}
