<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CloverWebApiTest extends TestCase
{
  use RefreshDatabase;

  function test_an_unauthorized_clover_client_cannot_add_token_data() {
    $this->withExceptionHandling();
    $profile = create('App\Profile');

    $data = [
      'account_type' => 'clover',
      'token' => 'fake_token',
      'merchant_id' => 'fake_merchant_id'
    ];

    $this->patch("/api/mobile/pay/business", $data)->assertStatus(401);
  }

  function test_an_authorized_user_can_add_token_data_for_first_time() {
    $user = create('App\User');
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['user_id' => $user->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);

    $data = [
      'account_type' => 'clover',
      'token' => 'fake_token',
      'merchant_id' => 'fake_merchant_id'
    ];

    $this->assertDatabaseMissing('connected_pos', ['profile_id' => $profile->id]);

    $response = $this->patch("/api/mobile/pay/business", $data, $this->headers($user))->getData();
    $this->assertEquals($response->data->connected_pos, 'clover');
    $this->assertDatabaseHas('connected_pos', ['profile_id' => $profile->id, 'account_type' => 'clover', 'token' => 'fake_token', 'merchant_id' => 'fake_merchant_id']);
  }

  function test_an_authorized_user_can_add_token_data_after_record_stored() {
    $user = create('App\User');
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['user_id' => $user->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $connectedPos = create('App\ConnectedPos', ['profile_id' => $profile->id, 'account_type' => 'clover', 'token' => 'fake_token_original', 'merchant_id' => 'fake_merchant_id_original']);

    $data = [
      'account_type' => 'clover',
      'token' => 'fake_token',
      'merchant_id' => 'fake_merchant_id'
    ];

    $this->assertDatabaseHas('connected_pos', ['profile_id' => $profile->id, 'account_type' => 'clover', 'token' => 'fake_token_original', 'merchant_id' => 'fake_merchant_id_original']);

    $response = $this->patch("/api/mobile/pay/business", $data, $this->headers($user))->getData();

    $this->assertEquals($response->data->connected_pos, 'clover');
    $this->assertDatabaseHas('connected_pos', ['profile_id' => $profile->id, 'account_type' => 'clover', 'token' => 'fake_token', 'merchant_id' => 'fake_merchant_id']);
  }
}
