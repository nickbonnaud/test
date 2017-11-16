<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use App\Events\TransactionError;
use Illuminate\Support\Facades\Notification;
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
  	$profile = create('App\Profile');
  	$user = create('App\User', ['default_tip_rate' => 20]);
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

  	$data = [
  		'id' => $transaction->id
  	];

  	$this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user));
  	$tip = ($user->default_tip_rate / 100) * $transaction->total;
  	$total = $transaction->total + $tip;
  	$this->assertDatabaseHas('transactions', ['tips' => $tip, 'total' => $total]);
	}

	function test_a_user_owning_transaction_can_confirm_a_bill_custom_tip() {
		Notification::fake();
  	$profile = create('App\Profile');
  	$user = create('App\User', ['default_tip_rate' => 20]);
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

  	$data = [
  		'id' => $transaction->id,
  		'tip' => 150
  	];

  	$this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user));
  	$tip = 150;
  	$total = $transaction->total + $tip;
  	$this->assertDatabaseHas('transactions', ['tips' => $tip, 'total' => $total]);
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
  		'bill_closed' => false
  	];

  	$this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user));
  	$this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'status' => 2, 'bill_closed' => false]);
	}
}
