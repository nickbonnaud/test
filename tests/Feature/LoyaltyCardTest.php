<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use App\Events\CustomerEarnReward;
use App\Events\CustomerRedeemItem;
use App\Notifications\LoyaltyRewardEarned;
use App\Notifications\CustomerRedeemReward;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoyaltyCardTest extends TestCase
{
  use RefreshDatabase;

  function test_a_notification_is_not_sent_if_reward_not_earned() {
		Mail::fake();
		Notification::fake();
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'reward' => 'free coffee', 'is_increment' => true, 'purchases_required' => 2]);
    $account = create('App\Account', ['profile_id' => $profile->id, 'splash_id' => 't1_mer_5a10dfa09f4b06f5e326d8b']);
    $user = create('App\User', ['default_tip_rate' => 20, 'customer_id' => 'c793a464b8d3085506e1e82378656dbb']);
    create('App\PushToken', ['user_id' => $user->id]);
    $loyaltyCard = create('App\LoyaltyCard', ['user_id' => $user->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0]);
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

    $data = [
      'id' => $transaction->id
    ];

    $this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user));
    Notification::assertNotSentTo(
      [$user],
      LoyaltyRewardEarned::class
    );
  }

  function test_a_notification_is_sent_if_reward_earned_increment() {
		Mail::fake();
		Notification::fake();
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'reward' => 'free coffee', 'is_increment' => true, 'purchases_required' => 2]);
    $account = create('App\Account', ['profile_id' => $profile->id, 'splash_id' => 't1_mer_5a10dfa09f4b06f5e326d8b']);
    $user = create('App\User', ['default_tip_rate' => 20, 'customer_id' => 'c793a464b8d3085506e1e82378656dbb']);
    create('App\PushToken', ['user_id' => $user->id]);
    $loyaltyCard = create('App\LoyaltyCard', ['user_id' => $user->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 1]);
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

    $data = [
      'id' => $transaction->id
    ];

    $this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user));
    Notification::assertSentTo(
      [$user],
      LoyaltyRewardEarned::class
    );
  }

  function test_a_notification_is_sent_if_reward_earned_amount() {
		Mail::fake();
		Notification::fake();
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'reward' => 'free coffee', 'is_increment' => false, 'amount_required' => 50]);
    $account = create('App\Account', ['profile_id' => $profile->id, 'splash_id' => 't1_mer_5a10dfa09f4b06f5e326d8b']);
    $user = create('App\User', ['default_tip_rate' => 20, 'customer_id' => 'c793a464b8d3085506e1e82378656dbb']);
    create('App\PushToken', ['user_id' => $user->id]);
    $loyaltyCard = create('App\LoyaltyCard', ['user_id' => $user->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0]);
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

    $data = [
      'id' => $transaction->id
    ];

    $this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user));
    Notification::assertSentTo(
      [$user],
      LoyaltyRewardEarned::class
    );
  }

  function test_an_event_is_sent_if_reward_earned_increment() {
		Mail::fake();
		Notification::fake();
		$this->expectsEvents(CustomerEarnReward::class);
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'reward' => 'free coffee', 'is_increment' => true, 'purchases_required' => 2]);
    $account = create('App\Account', ['profile_id' => $profile->id, 'splash_id' => 't1_mer_5a10dfa09f4b06f5e326d8b']);
    $user = create('App\User', ['default_tip_rate' => 20, 'customer_id' => 'c793a464b8d3085506e1e82378656dbb']);
    create('App\PushToken', ['user_id' => $user->id]);
    $loyaltyCard = create('App\LoyaltyCard', ['user_id' => $user->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 1]);
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

    $data = [
      'id' => $transaction->id
    ];

    $this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user));
  }

  function test_an_event_is_sent_if_reward_earned_amount() {
		Mail::fake();
		Notification::fake();
		$this->expectsEvents(CustomerEarnReward::class);
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'reward' => 'free coffee', 'is_increment' => false, 'amount_required' => 50]);
    $account = create('App\Account', ['profile_id' => $profile->id, 'splash_id' => 't1_mer_5a10dfa09f4b06f5e326d8b']);
    $user = create('App\User', ['default_tip_rate' => 20, 'customer_id' => 'c793a464b8d3085506e1e82378656dbb']);
    create('App\PushToken', ['user_id' => $user->id]);
    $loyaltyCard = create('App\LoyaltyCard', ['user_id' => $user->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0]);
    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

    $data = [
      'id' => $transaction->id
    ];

    $this->patch("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user));
  }

  function test_an_unauthorized_user_cannot_send_redeem_reward_notif_to_user() {
    $this->withExceptionHandling();
    $profile = create('App\Profile');
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'reward' => 'free coffee', 'is_increment' => true, 'purchases_required' => 20]);
    $user = create('App\User');
    $loyaltyCard = create('App\LoyaltyCard', ['user_id' => $user->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0, 'unredeemed_rewards' => 1]);

    $data = [
      'redeem_reward' => true
    ];

    $this->patch("/api/web/loyalty-card/{$profile->slug}/{$loyaltyCard->id}", $data)->assertRedirect('/login');
    $this->signIn();
    $this->patch("/api/web/loyalty-card/{$profile->slug}/{$loyaltyCard->id}", $data)->assertStatus(403);
  }

  function test_authorized_user_cannot_send_redeem_reward_notif_if_user_not_have_unredeemed_rewards() {
    Notification::fake();
    $this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'reward' => 'free coffee', 'is_increment' => true, 'purchases_required' => 20]);
    $user = create('App\User');
    $loyaltyCard = create('App\LoyaltyCard', ['user_id' => $user->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0, 'unredeemed_rewards' => 0]);

    $data = [
      'redeem_reward' => true
    ];

    $this->patch("/api/web/loyalty-card/{$profile->slug}/{$loyaltyCard->id}", $data);
    Notification::assertNotSentTo(
      [$user],
      CustomerRedeemReward::class
    );
  }

  function test_authorized_user_can_send_redeem_reward_notif_if_user_have_unredeemed_rewards() {
    Notification::fake();
    $this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'reward' => 'free coffee', 'is_increment' => true, 'purchases_required' => 20]);
    $user = create('App\User');
    $loyaltyCard = create('App\LoyaltyCard', ['user_id' => $user->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0, 'unredeemed_rewards' => 1]);

    $data = [
      'redeem_reward' => true
    ];

    $this->patch("/api/web/loyalty-card/{$profile->slug}/{$loyaltyCard->id}", $data);
    Notification::assertSentTo(
      [$user],
      CustomerRedeemReward::class
    );
  }

  function test_an_unauthorized_mobile_user_cannot_redeem_reward() {
    $this->withExceptionHandling();
    $profile = create('App\Profile');
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'reward' => 'free coffee', 'is_increment' => true, 'purchases_required' => 20]);
    $user = create('App\User');
    $loyaltyCard = create('App\LoyaltyCard', ['user_id' => $user->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0, 'unredeemed_rewards' => 1]);

    $data = [
      'unredeemed_rewards' => 0
    ];

    $this->json('PATCH', "/api/mobile/loyalty/{$loyaltyCard->id}", $data)->assertStatus(401);
  }

  function test_a_mobile_user_who_does_not_own_loyalty_card_cannot_redeem_reward() {
    $this->withExceptionHandling();
    $profile = create('App\Profile');
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'reward' => 'free coffee', 'is_increment' => true, 'purchases_required' => 20]);
    $user = create('App\User');
    $loyaltyCard = create('App\LoyaltyCard', ['user_id' => $user->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0, 'unredeemed_rewards' => 1]);

    $data = [
      'unredeemed_rewards' => 0
    ];

    $unAuthUser = create('App\User');
    $this->json('PATCH', "/api/mobile/loyalty/{$loyaltyCard->id}", $data, $this->headers($unAuthUser))->assertStatus(401);
  }

  function test_an_authorized_mobile_user_can_redeem_reward() {
    $this->expectsEvents(CustomerRedeemItem::class);
    $profile = create('App\Profile');
    $loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id, 'reward' => 'free coffee', 'is_increment' => true, 'purchases_required' => 20]);
    $user = create('App\User');
    $loyaltyCard = create('App\LoyaltyCard', ['user_id' => $user->id, 'loyalty_program_id' => $loyaltyProgram->id, 'current_amount' => 0, 'unredeemed_rewards' => 1]);

    $data = [
      'unredeemed_rewards' => 0
    ];

    $response = $this->json('PATCH', "/api/mobile/loyalty/{$loyaltyCard->id}", $data, $this->headers($user))->getData();

    $this->assertEquals(true, $response->success);
    $this->assertEquals(0, $loyaltyCard->fresh()->unredeemed_rewards);
  }
}
