<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use App\Events\TransactionError;
use App\Events\CustomerRequestBill;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTransactionTest extends TestCase
{
	use RefreshDatabase;

	function test_unauthorized_users_cannot_retrieve_users_recent_transactions() {
		$this->withExceptionHandling();
		Notification::fake();
		$user = create('App\User');
		$profile = create('App\Profile');

		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, ]);

		$this->json('GET', '/api/mobile/transactions?recent=1')->assertStatus(401);
	}

	function test_a_user_not_owning_transactions_cannot_retrieve_recent_transactions() {
		Notification::fake();
		$user = create('App\User');
		$unAuthorizedUser = create('App\User');

		$profile = create('App\Profile');

		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, ]);

		$response = $this->get('/api/mobile/transactions?recent=1', $this->headers($unAuthorizedUser))->getData();
		$this->assertCount(0, $response->data);
	}

	function test_a_user_owning_transactions_can_retrieve_recent_transactions() {
		Notification::fake();
		$user = create('App\User');
		$photo = create('App\Photo');
		$profile = create('App\Profile', ['logo_photo_id' => $photo->id]);
		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, ]);

		$profileOne = create('App\Profile', ['logo_photo_id' => $photo->id]);
		$transaction = create('App\Transaction', ['profile_id' => $profileOne->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, ]);

		$notUser = create('App\User');
		$transactionNotUser = create('App\Transaction', ['profile_id' => $profileOne->id, 'user_id' => $notUser
			->id, 'paid' => true, 'refund_full' => false, 'status' => 20, ]);


		$response = $this->get('/api/mobile/transactions?recent=1', $this->headers($user))->getData();
		$this->assertCount(2, $response->data);
	}

	function test_an_unauthorized_user_cannot_retrieve_pending_transactions() {
		$this->withExceptionHandling();
		Notification::fake();
		$user = create('App\User');
		$profile = create('App\Profile');

		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => false, 'is_refund' => false, 'status' => 10, ]);

		$this->get('/api/mobile/transactions?customerPending=1')->assertStatus(401);
	}

	function test_a_user_not_owning_transactions_cannot_retrieve_pending_transactions() {
		Notification::fake();
		$user = create('App\User');
		$unAuthorizedUser = create('App\User');

		$profile = create('App\Profile');

		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => false, 'is_refund' => false, 'status' => 10, ]);

		$response = $this->get('/api/mobile/transactions?customerPending=1', $this->headers($unAuthorizedUser))->getData();

		$this->assertCount(0, $response->data);
	}

	function test_a_user_owning_transactions_can_retrieve_pending_transactions() {
		Notification::fake();
		$user = create('App\User');
		$photo = create('App\Photo');
		$profile = create('App\Profile', ['logo_photo_id' => $photo->id]);

		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => false, 'is_refund' => false, 'status' => 10, ]);

		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'bill_closed' => true, 'is_refund' => false, 'status' => 10, ]);

		$response = $this->get('/api/mobile/transactions?customerPending=1', $this->headers($user))->getData();

		$this->assertCount(1, $response->data);
	}

	function test_an_authorized_user_can_notify_business_that_their_bill_is_wrong() {
		Notification::fake();
		$this->expectsEvents(TransactionError::class);
		$photo = create('App\Photo');
		$user = create('App\User');
		$profile = create('App\Profile', ['logo_photo_id' => $photo->id]);
		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => false, 'is_refund' => false, 'status' => 10, ]);

		$data = [
      'id' => $transaction->id,
      'status' => 3,
      'paid' => false,
      'bill_closed' => false
    ];

		$response = $this->json("PATCH", "/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user))->getData();
		$this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'status' => 3, 'paid' => false]);
	}

	function test_an_authorized_user_can_request_their_bill() {
		Notification::fake();
		$this->expectsEvents(CustomerRequestBill::class);
		$photo = create('App\Photo');
		$user = create('App\User');
		$profile = create('App\Profile', ['logo_photo_id' => $photo->id]);
		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => false, 'is_refund' => false, 'status' => 10, ]);

		$data = [
      'id' => $transaction->id,
      'status' => 12,
      'paid' => false,
      'bill_closed' => false
    ];

		$response = $this->json("PATCH", "/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user))->getData();
		$this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'status' => 12, 'paid' => false]);
	}


}
