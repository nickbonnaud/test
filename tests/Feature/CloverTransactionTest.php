<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Notifications\TransactionBillWasClosed;
use Illuminate\Support\Facades\Notification;
use App\Events\CustomerBillUpdate;
use Illuminate\Support\Facades\Event;
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

    $this->assertEquals($cloverId, $response->data[0]->pos_transaction_id);
    $this->assertEquals($profile->id, $response->data[0]->business_id);
    $this->assertEquals($user->id, $response->data[0]->customer_id);
  }

  function test_an_unauthorized_clover_client_cannot_post_a_transaction() {
    $this->withExceptionHandling();
    $profile = create('App\Profile');
    $user = create('App\User');
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'pos_transaction_id' => '1234qwer', 'paid' => false]);

    $data = [
      'pos_type' => 'clover',
      'pos_transaction_id' => '1234qwer',
      'user_id' => $user->id,
      'total' => $transaction->total,
      'tax' => $transaction->tax,
      'transaction_id' => $transaction->id
    ];

    $response = $this->post("/api/mobile/pay/transaction", $data)->assertStatus(401);
  }

  function test_an_authorized_clover_client_can_post_transaction_previously_created_no_total_discrepancy() {
    Notification::fake();
    $this->expectsEvents(CustomerBillUpdate::class);
    $authUser = create('App\User');
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['user_id' => $authUser->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $user = create('App\User');
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'pos_transaction_id' => '1234qwer', 'paid' => false]);

    $data = [
      'pos_type' => 'clover',
      'pos_transaction_id' => '1234qwer',
      'user_id' => $user->id,
      'total' => $transaction->total,
      'tax' => $transaction->tax,
      'transaction_id' => $transaction->id
    ];

    $response = $this->post("/api/mobile/pay/transaction", $data, $this->headers($authUser))->getData();
    $this->assertEquals('waiting_customer_approval', $response->success);
    Notification::assertSentTo(
      [$user],
      TransactionBillWasClosed::class
    );
  }

  function test_an_authorized_client_can_post_transaction_previously_create_with_discrepancy() {
    Notification::fake();
    $this->expectsEvents(CustomerBillUpdate::class);
    $authUser = create('App\User');
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['user_id' => $authUser->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $connectedPos = create('App\ConnectedPos', ['profile_id' => $profile->id, 'token' => '021c9330-2e51-46c2-24d0-a4bd7f46489c', 'merchant_id' => 'RR9ACXMZ6AFA1']);
    $user = create('App\User');
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'pos_transaction_id' => 'MSX2EDKAZQ0A8', 'paid' => false]);

    $data = [
      'pos_type' => 'clover',
      'pos_transaction_id' => 'MSX2EDKAZQ0A8',
      'user_id' => $user->id,
      'total' => 1398,
      'tax' => 98,
      'transaction_id' => $transaction->id
    ];

    $response = $this->post("/api/mobile/pay/transaction", $data, $this->headers($authUser))->getData();
    $this->assertEquals('waiting_customer_approval', $response->success);
    $this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'total' => 1398, 'tax' => 98]);
    Notification::assertSentTo(
      [$user],
      TransactionBillWasClosed::class
    );
  }

  function test_an_authorized_client_can_post_transaction_no_previous_record() {
    Notification::fake();
    $this->expectsEvents(CustomerBillUpdate::class);
    $authUser = create('App\User');
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['user_id' => $authUser->id, 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $connectedPos = create('App\ConnectedPos', ['profile_id' => $profile->id, 'token' => '021c9330-2e51-46c2-24d0-a4bd7f46489c', 'merchant_id' => 'RR9ACXMZ6AFA1']);
    $user = create('App\User');

    $data = [
      'pos_type' => 'clover',
      'pos_transaction_id' => 'MSX2EDKAZQ0A8',
      'user_id' => $user->id,
      'total' => 1398,
      'tax' => 98,
      'transaction_id' =>null
    ];

    $response = $this->post("/api/mobile/pay/transaction", $data, $this->headers($authUser))->getData();
    $this->assertEquals('waiting_customer_approval', $response->success);
    $this->assertDatabaseHas('transactions', ['total' => 1398, 'tax' => 98, 'pos_transaction_id' => 'MSX2EDKAZQ0A8']);
    Notification::assertSentTo(
      [$user],
      TransactionBillWasClosed::class
    );
  }
}
