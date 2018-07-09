<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CloverTransactionTest extends TestCase
{
  use RefreshDatabase;

  function test_an_unauthorized_clover_client_cannot_retrieve_a_clover_transaction() {
    $this->withExceptionHandling();
    $profile = create('App\Profile');
    $user = create('App\User');

    $cloverId = '1234qwer';

    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'pos_transaction_id' => $cloverId]);

    $response = $this->get("/api/mobile/pay/transaction?clover={$cloverId}")->assertStatus(401);
  }

  function test_an_authorized_clover_client_can_retrieve_a_clover_transaction() {
    $authUser = create('App\User');
    $profile = create('App\Profile', ['user_id' => $authUser->id]);
    $user = create('App\User');

    $cloverId = '1234qwer';

    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'pos_transaction_id' => $cloverId, 'paid' => false]);

    $response = $this->get("/api/mobile/pay/transaction?clover={$cloverId}", $this->headers($authUser))->getData();

    dd($response);

    $this->assertEquals($cloverId, $response->data[0]->pos_transaction_id);
    $this->assertEquals($profile->id, $response->data[0]->business_id);
    $this->assertEquals($user->id, $response->data[0]->customer_id);
  }
}
