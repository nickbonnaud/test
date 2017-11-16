<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiDealTest extends TestCase
{
	use RefreshDatabase;

	function test_unauthorized_users_cannot_retrieve_users_deals() {
		$this->withExceptionHandling();
		Notification::fake();
		$user = create('App\User');
		$profile = create('App\Profile');
		$post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 1000, 'end_date' => Carbon::tomorrow()]);

		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

		$this->json('GET', '/api/mobile/transactions?deals=1')->assertStatus(401);
	}

	function test_a_user_not_owning_deals_cannot_retrieve_deals() {
		Notification::fake();
		$user = create('App\User');
		$unAuthorizedUser = create('App\User');

		$profile = create('App\Profile');
		$post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 1000, 'end_date' => Carbon::tomorrow()]);

		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

		$response = $this->get('/api/mobile/transactions?deals=1', $this->headers($unAuthorizedUser))->getData();
		$this->assertCount(0, $response->data);
	}

	function test_a_user_owning_deals_can_retrieve_deals() {
		Notification::fake();
		$user = create('App\User');
		$photo = create('App\Photo');
		$profile = create('App\Profile', ['logo_photo_id' => $photo->id]);
		$post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 1000, 'end_date' => Carbon::tomorrow(), 'photo_id' => $photo->id]);

		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

		$profileOne = create('App\Profile', ['logo_photo_id' => $photo->id]);
		$transaction = create('App\Transaction', ['profile_id' => $profileOne->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => true]);

		$notUser = create('App\User');
		$transactionNotUser = create('App\Transaction', ['profile_id' => $profileOne->id, 'user_id' => $notUser->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => true]);


		$response = $this->get('/api/mobile/transactions?deals=1', $this->headers($user))->getData();
		$this->assertCount(2, $response->data);
	}
}
