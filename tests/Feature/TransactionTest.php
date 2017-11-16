<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_retrieve_a_profiles_transactions_pending() {
		$this->withExceptionHandling();
  	$profile = create('App\Profile');
  	$user = create('App\User');
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id]);

  	$this->get("/api/web/transactions/{$profile->slug}?pending=1")->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/api/web/transactions/{$profile->slug}?pending=1")->assertStatus(403);
	}

	function test_an_authorized_user_can_retrieve_a_profiles_transactions_pending() {
		$this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->id()]);
  	$user = create('App\User');
  	$transactionFirst = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id]);
    $transactionFirst->status = 11;
    $transactionFirst->save();
  	$transactionSecond = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id]);
    $transactionSecond->status = 20;
    $transactionSecond->save();

  	$response = $this->get("/api/web/transactions/{$profile->slug}?pending=1")->getData();
  	$this->assertEquals($response->data[0]->id, $transactionFirst->id);
  	$this->assertCount(1, $response->data);
	}

  function test_an_authorized_user_can_retrieve_a_profiles_transactions_finalized() {
    $this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->id()]);
    $user = create('App\User');
    $transactionFirst = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id]);
    $transactionFirst->status = 11;
    $transactionFirst->save();
    $transactionSecond = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id]);
    $transactionSecond->status = 20;
    $transactionSecond->save();

    $response = $this->get("/api/web/transactions/{$profile->slug}?finalized=1")->getData();
    $this->assertEquals($response->data[0]->id, $transactionSecond->id);
    $this->assertCount(1, $response->data);
  }

  function test_an_unauthorized_user_cannot_redeem_a_customers_deal() {
    $this->withExceptionHandling();
    $profile = create('App\Profile');
    $user = create('App\User');
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'redeemed' => false]);

    $data = ['redeemed' => true];
    $this->patch("/api/web/transactions/{$profile->slug}/{$transaction->id}", $data)->assertRedirect('/login');
    $this->signIn();
    $this->patch("/api/web/transactions/{$profile->slug}/{$transaction->id}", $data)->assertStatus(403);
  }


  function test_an_authorized_user_can_redeem_a_customers_deal() {
    $this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->id()]);
    $user = create('App\User');
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'redeemed' => false]);

    $data = ['redeemed' => true];
    $response = $this->patch("/api/web/transactions/{$profile->slug}/{$transaction->id}", $data)->getData();
    $this->assertEquals($response->success, true);
  }

  function test_an_unauthorized_user_cannot_create_a_transaction() {
    $this->withExceptionHandling();
    $profile = create('App\Profile');
    $user = create('App\User');
    $transaction = make('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => false, 'is_refund' => false]);

    $this->post("/api/web/transactions/{$profile->slug}/{$user->id}", $transaction->toArray())->assertRedirect('/login');
    $this->signIn();
    $this->post("/api/web/transactions/{$profile->slug}/{$user->id}", $transaction->toArray())->assertStatus(403);
  }

  function test_an_authorized_user_can_create_a_transaction() {
    Notification::fake();
    $this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->id()]);
    $user = create('App\User');
    
    $data = [
      'user_id' => $user->id,
      'profile_id' => $profile->id,
      'products' => 'pizza',
      'tax' => 100,
      'net_sales' => 200,
      'total' => 300,
      'bill_closed' => true
    ];

    $response = $this->post("/api/web/transactions/{$profile->slug}/{$user->id}", $data)->getData();
    $this->assertEquals($response->success, true);
    $this->assertDatabaseHas('transactions', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  }

  function test_an_unauthorized_user_cannot_update_a_transaction() {
    $this->withExceptionHandling();
    $profile = create('App\Profile');
    $user = create('App\User');
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => false, 'is_refund' => false]);

    $data = ['bill_closed' => true];
    $this->patch("/api/web/transactions/{$profile->slug}/{$transaction->id}", $data)->assertRedirect('/login');
    $this->signIn();
    $this->patch("/api/web/transactions/{$profile->slug}/{$transaction->id}", $data)->assertStatus(403);
  }

  function test_an_authorized_user_can_update_a_transaction() {
   Notification::fake();
    $this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->id()]);
    $user = create('App\User');
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => false, 'is_refund' => false]);

    $this->assertDatabaseHas('transactions', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => false]);
    $data = ['bill_closed' => true];
    $response = $this->patch("/api/web/transactions/{$profile->slug}/{$transaction->id}", $data)->getData();
    $this->assertEquals($response->success, true);
    $this->assertEquals($response->transaction->bill_closed, true);
  }
}
