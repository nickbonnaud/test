<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomerRedeemReward;

class CloverLoyaltyTest extends TestCase
{
  use RefreshDatabase;

  function test_an_authorized_clover_client_redeem_a_loyalty_reward() {
  	Notification::fake();
    $user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'is_increment' => true, 'purchases_required' => 2]);

  	$photo = create('App\Photo');
  	$customer = create('App\User', ["photo_id" => $photo->id]);
  	$loyaltyCard = create('App\LoyaltyCard', ['user_id' => $customer->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0, 'unredeemed_rewards' => 1, 'rewards_achieved' => 0]);

  	$data = [
  		'id' => $loyaltyCard->id
  	];

  	$response = $this->patch("/api/mobile/pay/loyalty", $data, $this->headers($user))->getData();
  	$this->assertEquals('waiting_customer_approval', $response->success);
  	Notification::assertSentTo(
      [$customer],
      CustomerRedeemReward::class
    );
  }

  function test_an_unauthorized_clover_client_cannot_redeem_loyalty_reward() {
    Notification::fake();
    $this->withExceptionHandling();
    $user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'is_increment' => true, 'purchases_required' => 2]);

  	$photo = create('App\Photo');
  	$customer = create('App\User', ["photo_id" => $photo->id]);
  	$loyaltyCard = create('App\LoyaltyCard', ['user_id' => $customer->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0, 'unredeemed_rewards' => 1, 'rewards_achieved' => 0]);

  	$data = [
  		'id' => $loyaltyCard->id
  	];
  	$response = $this->patch("/api/mobile/pay/loyalty", $data)->assertStatus(401);
  }

  function test_a_clover_client_cannot_redeem_loyalty_reward_that_does_not_exist() {
    Notification::fake();
    $this->withExceptionHandling();
   	$user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'is_increment' => true, 'purchases_required' => 2]);

  	$photo = create('App\Photo');
  	$customer = create('App\User', ["photo_id" => $photo->id]);
  	$loyaltyCard = create('App\LoyaltyCard', ['user_id' => $customer->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0, 'unredeemed_rewards' => 1, 'rewards_achieved' => 0]);


  	$data = [
  		'id' => 5
  	];
  	$response = $this->patch("/api/mobile/pay/loyalty", $data, $this->headers($user))->assertStatus(404);
  }

  function test_a_clover_client_cannot_redeem_loyalty_reward_that_has_already_been_redeemed() {
    Notification::fake();
    $this->withExceptionHandling();
    $user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'is_increment' => true, 'purchases_required' => 2]);

  	$photo = create('App\Photo');
  	$customer = create('App\User', ["photo_id" => $photo->id]);
  	$loyaltyCard = create('App\LoyaltyCard', ['user_id' => $customer->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0, 'unredeemed_rewards' => 0, 'rewards_achieved' => 0]);


  	$data = [
  		'id' => $loyaltyCard->id
  	];
  	$response = $this->patch("/api/mobile/pay/loyalty", $data, $this->headers($user))->assertStatus(403);
  }


  function test_a_clover_client_cannot_redeem_loyalty_card_that_it_does_not_own() {
    Notification::fake();
    $this->withExceptionHandling();
    $user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'is_increment' => true, 'purchases_required' => 2]);

    $userNotOwned = create('App\User');
    $profileNotOwned = create('App\Profile', ['user_id' => $userNotOwned->id]);
    $loyaltyProgramNotOwned = create('App\LoyaltyProgram', ['profile_id' => $profileNotOwned->id, 'is_increment' => true, 'purchases_required' => 2]);

  	$photo = create('App\Photo');
  	$customer = create('App\User', ["photo_id" => $photo->id]);
  	$loyaltyCard = create('App\LoyaltyCard', ['user_id' => $customer->id, 'loyalty_program_id' => $loyaltyProgramNotOwned->id, 'current_amount' => 0, 'unredeemed_rewards' => 1, 'rewards_achieved' => 0]);


  	$data = [
  		'id' => $loyaltyCard->id
  	];
  	$response = $this->patch("/api/mobile/pay/loyalty", $data, $this->headers($user))->assertStatus(403);
  }
}
