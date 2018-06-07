<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomerRedeemDeal;

class CloverDealTest extends TestCase
{
  use RefreshDatabase;

  function test_an_authorized_clover_client_redeem_a_deal() {
  	Notification::fake();
    $user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 1000, 'end_date' => Carbon::tomorrow()]);
  	$photo = create('App\Photo');
  	$customer = create('App\User', ["photo_id" => $photo->id]);
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $customer->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

  	create('App\UserLocation', ['profile_id' => $profile->id, 'user_id' => $customer->id]);

  	$data = [
  		'id' => $transaction->id
  	];

  	$response = $this->patch("/api/mobile/pay/deal", $data, $this->headers($user))->getData();
  	$this->assertEquals('waiting_customer_approval', $response->success);
  	Notification::assertSentTo(
      [$customer],
      CustomerRedeemDeal::class
    );
  }

  function test_an_unauthorized_clover_client_cannot_redeem_deal() {
    Notification::fake();
    $this->withExceptionHandling();
    $user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 1000, 'end_date' => Carbon::tomorrow()]);
  	$photo = create('App\Photo');
  	$customer = create('App\User', ["photo_id" => $photo->id]);
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $customer->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);

  	$data = [
  		'id' => $transaction->id
  	];
  	$response = $this->patch("/api/mobile/pay/deal", $data)->assertStatus(401);
  }

  function test_a_clover_client_cannot_redeem_deal_that_does_not_exist() {
    Notification::fake();
    $this->withExceptionHandling();
    $user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 1000, 'end_date' => Carbon::tomorrow()]);
  	$photo = create('App\Photo');
  	$customer = create('App\User', ["photo_id" => $photo->id]);


  	$data = [
  		'id' => 5
  	];
  	$response = $this->patch("/api/mobile/pay/deal", $data, $this->headers($user))->assertStatus(404);
  }

  function test_a_clover_client_cannot_redeem_deal_that_has_already_been_redeemed() {
    Notification::fake();
    $this->withExceptionHandling();
    $user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);
    $post = create('App\Post', ['profile_id' => $profile->id, 'is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 1000, 'end_date' => Carbon::tomorrow()]);
  	$photo = create('App\Photo');
  	$customer = create('App\User', ["photo_id" => $photo->id]);
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $customer->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => true]);


  	$data = [
  		'id' => $transaction->id
  	];
  	$response = $this->patch("/api/mobile/pay/deal", $data, $this->headers($user))->assertStatus(403);
  }


  function test_a_clover_client_cannot_redeem_deal_that_it_does_not_own() {
    Notification::fake();
    $this->withExceptionHandling();
    $user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
  	$profileWithDeal = create('App\Profile');
    $account = create('App\Account', ['profile_id' => $profileWithDeal->id]);
    $post = create('App\Post', ['profile_id' => $profileWithDeal->id, 'is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 1000, 'end_date' => Carbon::tomorrow()]);
  	$photo = create('App\Photo');
  	$customer = create('App\User', ["photo_id" => $photo->id]);
  	$transaction = create('App\Transaction', ['profile_id' => $profileWithDeal->id, 'user_id' => $customer->id, 'paid' => true, 'refund_full' => false, 'status' => 20, 'deal_id' => $post->id, 'redeemed' => false]);


  	$data = [
  		'id' => $transaction->id
  	];
  	$response = $this->patch("/api/mobile/pay/deal", $data, $this->headers($user))->assertStatus(403);
  }
}
