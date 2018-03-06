<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Event;
use App\Notifications\CustomerRedeemDeal;
use App\Events\CustomerRedeemItem;
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
		$post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 1000, 'end_date' => Carbon::tomorrow(), 'photo_id' => $photo->id, 'message' => 'Get this awesome deal now!']);

		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

		$profileOne = create('App\Profile', ['logo_photo_id' => $photo->id]);
		$transaction = create('App\Transaction', ['profile_id' => $profileOne->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => true]);

		$notUser = create('App\User');
		$transactionNotUser = create('App\Transaction', ['profile_id' => $profileOne->id, 'user_id' => $notUser->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => true]);


		$response = $this->get('/api/mobile/transactions?deals=1', $this->headers($user))->getData();
		$this->assertCount(2, $response->data);
	}

  function test_an_unauthorized_user_cannot_retrieve_all_users_purchased_deals() {
    $this->withExceptionHandling();
    Notification::fake();
    $user = create('App\User');
    $profile = create('App\Profile');
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 1000, 'end_date' => Carbon::tomorrow()]);

    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

    $this->json('GET', '/api/mobile/transactions?&dealsAll=1')->assertStatus(401);
  }

  function test_a_user_not_owning_deals_cannot_retrieve_all_users_purchased_deals() {
    Notification::fake();
    $user = create('App\User');
    $unAuthorizedUser = create('App\User');

    $profile = create('App\Profile');
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 1000, 'end_date' => Carbon::tomorrow()]);

    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

    $response = $this->get('/api/mobile/transactions?dealsAll=1', $this->headers($unAuthorizedUser))->getData();
    $this->assertCount(0, $response->data);
  }

  function test_a_user_owning_deals_can_retrieve_all_users_purchased_deals() {
    Notification::fake();
    $user = create('App\User');
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['logo_photo_id' => $photo->id]);
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 1000, 'end_date' => Carbon::tomorrow(), 'photo_id' => $photo->id, 'message' => 'Get this awesome deal now!']);

    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

    $profileOne = create('App\Profile', ['logo_photo_id' => $photo->id]);
    $transaction = create('App\Transaction', ['profile_id' => $profileOne->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => true]);

    $notUser = create('App\User');
    $transactionNotUser = create('App\Transaction', ['profile_id' => $profileOne->id, 'user_id' => $notUser->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => true]);


    $response = $this->get('/api/mobile/transactions?dealsAll=1', $this->headers($user))->getData();
    $this->assertCount(2, $response->data);
  }

	function test_an_unauthorized_user_cannot_send_redeem_deal_notif_to_user() {
    Notification::fake();
    $this->withExceptionHandling();
    $profile = create('App\Profile');
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'free coffee', 'price' => 100, 'end_date' => Carbon::tomorrow()]);

		$user = create('App\User');
		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

    $data = [
      'redeem_deal' => true
    ];

    $this->patch("/api/web/deals/{$profile->slug}/{$transaction->id}", $data)->assertRedirect('/login');
    $this->signIn();
    $this->patch("/api/web/deals/{$profile->slug}/{$transaction->id}", $data)->assertStatus(403);
  }

  function test_authorized_user_cannot_send_redeem_deal_notif_if_user_already_redeemed() {
  	Notification::fake();
    $this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'free coffee', 'price' => 100, 'end_date' => Carbon::tomorrow()]);

		$user = create('App\User');
		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => true]);

    $data = [
      'redeem_deal' => true
    ];

    $this->patch("/api/web/deals/{$profile->slug}/{$transaction->id}", $data);
    Notification::assertNotSentTo(
      [$user],
      CustomerRedeemDeal::class
    );
  }

  function test_authorized_user_can_send_redeem_deal_notif_if_user_have_not_redeemed_deal() {
  	Notification::fake();
    $this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'free coffee', 'price' => 100, 'end_date' => Carbon::tomorrow()]);

		$user = create('App\User');
		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

    $data = [
      'redeem_deal' => true
    ];

    $this->patch("/api/web/deals/{$profile->slug}/{$transaction->id}", $data);
    Notification::assertSentTo(
      [$user],
      CustomerRedeemDeal::class
    );
  }

  function test_an_unauthorized_mobile_user_cannot_redeem_deal() {
    $this->withExceptionHandling();
    $profile = create('App\Profile');
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'free coffee', 'price' => 100, 'end_date' => Carbon::tomorrow()]);

		$user = create('App\User');
		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

    $data = [
      'redeemed' => true
    ];

    $this->json('PATCH', "/api/mobile/deals/{$transaction->id}")->assertStatus(401);
  }

  function test_a_mobile_user_who_does_not_own_transaction_cannot_redeem_deal() {
  	$this->withExceptionHandling();
    $profile = create('App\Profile');
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'free coffee', 'price' => 100, 'end_date' => Carbon::tomorrow()]);

		$user = create('App\User');
		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

    $data = [
      'redeemed' => true
    ];

    $unAuthUser = create('App\User');
    $this->json('PATCH', "/api/mobile/deals/{$transaction->id}", $data, $this->headers($unAuthUser))->assertStatus(401);
  }

  function test_an_authorized_mobile_user_can_redeem_deal() {
    Event::fake();
    $profile = create('App\Profile');
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'free coffee', 'price' => 100, 'end_date' => Carbon::tomorrow()]);

		$user = create('App\User');
    $userLocation = create('App\UserLocation', ['user_id' => $user->id, 'profile_id' => $profile->id]);
		$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

    $data = [
      'redeemed' => true
    ];

    $response = $this->json('PATCH', "/api/mobile/deals/{$transaction->id}", $data, $this->headers($user))->getData();
    $this->assertEquals(true, $response->success);
    $this->assertEquals(true, $transaction->fresh()->redeemed);
  }

  function test_an_authorized_mobile_user_can_reject_deal_later() {
    $this->expectsEvents(CustomerRedeemItem::class);
    $profile = create('App\Profile');
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'free coffee', 'price' => 100, 'end_date' => Carbon::tomorrow()]);

    $user = create('App\User');
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

    $data = [
      'redeemed' => false,
      'issue' => 'redeem_later_deal'
    ];

    $response = $this->json('PATCH', "/api/mobile/deals/{$transaction->id}", $data, $this->headers($user))->getData();
    $this->assertEquals(true, $response->success);
    $this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'redeemed' => false]);
  }

  function test_an_authorized_mobile_user_can_reject_deal_not_theirs() {
    $this->expectsEvents(CustomerRedeemItem::class);
    $profile = create('App\Profile');
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'free coffee', 'price' => 100, 'end_date' => Carbon::tomorrow()]);

    $user = create('App\User');
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

    $data = [
      'redeemed' => false,
      'issue' => 'wrong_deal'
    ];

    $response = $this->json('PATCH', "/api/mobile/deals/{$transaction->id}", $data, $this->headers($user))->getData();
    $this->assertEquals(true, $response->success);
    $this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'redeemed' => false]);
  }
}
