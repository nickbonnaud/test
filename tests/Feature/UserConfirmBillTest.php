<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Mail\TransactionReceipt;
use App\Mail\TransactionErrorEmail;
use Illuminate\Support\Facades\Event;
use App\Events\TransactionError;
use App\Events\TransactionSuccess;
use App\Events\TransactionsChange;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserConfirmBillTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_confirm_a_bill() {
		$this->withExceptionHandling();
		Notification::fake();
  	$profile = create('App\Profile');
  	$user = create('App\User');
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

  	$data = [
  		'id' => $transaction->id,
  		'custom' => false,
  	];

  	$this->patch("/api/mobile/transactions/{$profile->slug}", $data)->assertStatus(401);
	}

	function test_a_user_not_owning_transaction_cannot_confirm_a_bill() {
		Notification::fake();
  	$profile = create('App\Profile');
  	$user = create('App\User');
  	$userBill = create('App\User');
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $userBill->id, 'bill_closed' => true, 'is_refund' => false]);

  	$data = [
  		'id' => $transaction->id
  	];

  	$this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user))->assertStatus(401);
	}

	function test_a_user_owning_transaction_can_confirm_a_bill_default_tip() {
		Notification::fake();
    Mail::fake();
    $this->expectsEvents(TransactionSuccess::class);
    $this->expectsEvents(TransactionsChange::class);
  	$profile = create('App\Profile');
    $account = create('App\Account', ['profile_id' => $profile->id, 'splashId' => 't1_mer_5a10dfa09f4b06f5e326d8b']);
  	$user = create('App\User', ['default_tip_rate' => 20, 'customer_id' => 'c793a464b8d3085506e1e82378656dbb']);
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

  	$data = [
  		'id' => $transaction->id
  	];

  	$response = $this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user))->getData();
    $this->assertEquals(true, $response->success);
    $this->assertEquals('user_pay', $response->type);
  	$tip = ($user->default_tip_rate / 100) * $transaction->total;
  	$total = $transaction->total + $tip;
  	$this->assertDatabaseHas('transactions', ['tips' => $tip, 'total' => $total, 'status' => 20, 'paid' => true]);
    $this->assertNotNull($transaction->fresh()->splash_id);
    Mail::assertSent(TransactionReceipt::class, function($mail) use ($profile, $transaction, $user) {
      return  $mail->profile->id == $profile->id &&
              $mail->transaction->id == $transaction->id &&
              $mail->hasTo($user->email);
    });
	}

	function test_a_user_owning_transaction_can_confirm_a_bill_custom_tip() {
		Notification::fake();
    Mail::fake();
    $this->expectsEvents(TransactionSuccess::class);
    $this->expectsEvents(TransactionsChange::class);
  	$profile = create('App\Profile');
    $account = create('App\Account', ['profile_id' => $profile->id, 'splashId' => 't1_mer_5a10dfa09f4b06f5e326d8b']);
  	$user = create('App\User', ['default_tip_rate' => 20, 'customer_id' => 'c793a464b8d3085506e1e82378656dbb']);
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

  	$data = [
  		'id' => $transaction->id,
  		'tip' => 150
  	];

  	$response = $this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user))->getData();
    $this->assertEquals(true, $response->success);
    $this->assertEquals('user_pay', $response->type);
  	$tip = 150;
  	$total = $transaction->total + $tip;
  	$this->assertDatabaseHas('transactions', ['tips' => $tip, 'total' => $total, 'status' => 20, 'paid' => true]);
    $this->assertNotNull($transaction->fresh()->splash_id);
    Mail::assertSent(TransactionReceipt::class, function($mail) use ($profile, $transaction, $user) {
      return  $mail->profile->id == $profile->id &&
              $mail->transaction->id == $transaction->id &&
              $mail->hasTo($user->email);
    });
	}

	function test_a_user_owning_transaction_can_reject_a_bill() {
		Notification::fake();
		$this->expectsEvents(TransactionError::class);
  	$profile = create('App\Profile');
  	$user = create('App\User', ['default_tip_rate' => 20]);
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

  	$data = [
  		'id' => $transaction->id,
  		'status' => 2,
      'paid' => false,
  		'bill_closed' => false
  	];

  	$this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user));
  	$this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'status' => 2, 'bill_closed' => false]);
	}

  function test_a_confirmed_transaction_that_fails_notifies_business_and_sends_fail_email_to_admin() {
    Notification::fake();
    Mail::fake();
    $this->expectsEvents(TransactionError::class);
    $this->expectsEvents(TransactionsChange::class);
    $profile = create('App\Profile');
    $account = create('App\Account', ['profile_id' => $profile->id, 'splashId' => 't1_mer_5a10dfa09f4b06f5e326d8b']);
    $user = create('App\User', ['default_tip_rate' => 20, 'customer_id' => 'c793a464b8d3085506e1e82378656db']);
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

    $data = [
      'id' => $transaction->id
    ];

    $response = $this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user))->getData();
    $this->assertEquals(false, $response->success);
    $this->assertEquals('user_pay', $response->type);
    $this->assertDatabaseHas('transactions', ['status' => 1, 'paid' => false]);
    Mail::assertSent(TransactionErrorEmail::class, function($mail) use ($profile, $transaction, $user) {
      return  $mail->profile->id == $profile->id &&
              $mail->transaction->id == $transaction->id &&
              $mail->user->id == $user->id &&
              $mail->hasTo(env('DEFAULT_EMAIL'));
    });
  }

  function test_confirming_bill_removes_notification_from_db() {
    Mail::fake();
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id] );
    $account = create('App\Account', ['profile_id' => $profile->id, 'splashId' => env('TEST_SPLASH_MERCHANT_ID')]);
    $user = create('App\User', ['default_tip_rate' => 20, 'customer_id' => env('TEST_SPLASH_CUSTOMER_ID')]);
    create('App\PushToken', ['user_id' => $user->id]);
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

    $this->assertDatabaseHas('notifications', ['id' => $transaction->fresh()->notification_id]);

    $data = [
      'id' => $transaction->id
    ];

    $response = $this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user))->getData();
    $this->assertDatabaseMissing('notifications', ['id' => $transaction->fresh()->notification_id]);
  }

  function test_denying_bill_marks_notification_as_read_in_db() {
    Mail::fake();
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id] );
    $account = create('App\Account', ['profile_id' => $profile->id, 'splashId' => 't1_mer_5a10dfa09f4b06f5e326d8b']);
    $user = create('App\User', ['default_tip_rate' => 20, 'customer_id' => 'c793a464b8d3085506e1e82378656dbb']);
    create('App\PushToken', ['user_id' => $user->id]);
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false, 'paid' => false ]);

    $this->assertDatabaseHas('notifications', ['id' => $transaction->fresh()->notification_id]);
    $notification = $user->notifications->where('id', '=', $transaction->fresh()->notification_id)->first();
    $this->assertNull($notification->fresh()->read_at);
    $data = [
      'id' => $transaction->id,
      'status' => 2,
      'paid' => false,
      'bill_closed' => false
    ];

    $this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user));
    $this->assertNotNull($notification->fresh()->read_at);
  }
}
