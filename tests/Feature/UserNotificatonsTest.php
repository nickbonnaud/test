<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Notifications\TransactionBillWasClosed;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserNotificatonsTest extends TestCase
{
	use RefreshDatabase;

	function test_a_transaction_that_is_open_does_not_prepares_a_user_notification_in_db() {
		$this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
  	$user = create('App\User');
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => false, 'is_refund' => false]);

  	$this->assertCount(0, $user->fresh()->notifications);
	}

	function test_a_transaction_that_is_closed_prepares_a_notification_in_db() {
		Notification::fake();
    $this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
  	$user = create('App\User');
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

    Notification::assertSentTo(
      [$user],
      TransactionBillWasClosed::class
    );
	}

  function test_a_transaction_that_is_a_refund_does_not_prepares_a_notification_in_db() {
    Notification::fake();
    $this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $user = create('App\User');
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => false, 'is_refund' => true]);

    Notification::assertNotSentTo(
      [$user],
      TransactionBillWasClosed::class
    );

    $transaction->bill_closed = true;
    $transaction->is_refund = true;
    $transaction->save();

    Notification::assertNotSentTo(
      [$user],
      TransactionBillWasClosed::class
    );
  }


	function test_a_transaction_that_is_open_does_not_prepare_notification_in_db() {
		Notification::fake();
    $this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
  	$user = create('App\User');
  	create('App\PushToken', ['user_id' => $user->id]);
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => false, 'is_refund' => false]);
  	Notification::assertNotSentTo(
      [$user],
      TransactionBillWasClosed::class
    );
	}

  function test_a_transaction_that_is_open_and_then_closed_does_prepares_notification_in_db() {
    Notification::fake();
    $this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $user = create('App\User');
    create('App\PushToken', ['user_id' => $user->id]);
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => false, 'is_refund' => false]);

    $transaction->bill_closed = true;
    $transaction->save();
    Notification::assertSentTo(
      [$user],
      TransactionBillWasClosed::class
    );
  }

	function test_a_transaction_that_is_closed_sends_a_notification() {
		$this->signIn();
  	$photo = create('App\Photo');
    $profile = create('App\Profile', ['user_id' => auth()->id(), 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
  	$user = create('App\User');
  	create('App\PushToken', ['user_id' => $user->id]);

  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'is_refund' => false, 'bill_closed' => true]);
    $status = $transaction->fresh()->status;
  	$this->assertDatabaseHas('transactions', ['status' => $status]);
	}

  function test_a_user_is_notified_once_when_they_enter_a_location() {
    $profile = create('App\Profile');
    $account = create('App\Account', ['profile_id' => $profile->id]);
    $user = create('App\User');
    create('App\PushToken', ['user_id' => $user->id]);

    create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user->id]);
    $this->assertCount(1, $user->fresh()->notifications);
    create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $user->id]);
    $this->assertCount(1, $user->fresh()->notifications);
  }
}
