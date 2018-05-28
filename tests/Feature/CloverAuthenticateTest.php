<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class CloverAuthenticateTest extends TestCase
{
  use RefreshDatabase;

  function test_a_clover_client_can_retrieve_their_data() {

  	$profile = create('App\Profile');
  	$account = create('App\Account', ['profile_id' => $profile->id]);

  	$response = $this->get("/api/mobile/pay/me")->getData();
  	dd($response);
  	$this->assertCount(2, $response->data);
  }
}
