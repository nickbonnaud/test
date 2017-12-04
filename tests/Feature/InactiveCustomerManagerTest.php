<?php

namespace Tests\Feature;

use Artisan;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PayOrKeepOpenNotification;
use App\Notifications\FixTransactionNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InactiveCustomerManagerTest extends TestCase
{
  use RefreshDatabase;
  
  public function test_manager_does_not_remove_customerLocations_who_are_active() {
  	Notification::fake();
  	Event::fake();
  	$user = create('App\User');
  	$profile = create('App\Profile');
  	create('App\UserLocation', ['user_id' => $user->id, 'profile_id' => $profile->id]);
  	$transaction = create('App\Transaction', ['user_id' => $user->id, 'profile_id' => $profile->id, 'paid' => false]);
  	Artisan::call('app:inactive_customer_manager');
  	$this->assertDatabaseHas('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  }

  public function test_manager_removes_customerLocations_who_are_inactive_with_not_transaction() {
  	Notification::fake();
  	Event::fake();
  	$user = create('App\User');
  	$profile = create('App\Profile');
  	create('App\UserLocation', ['user_id' => $user->id, 'profile_id' => $profile->id, 'updated_at' => Carbon::now()->subMinutes(30)]);
  	$this->assertDatabaseHas('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  	Artisan::call('app:inactive_customer_manager');
  	$this->assertDatabaseMissing('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  }

  public function test_manager_removes_customerLocations_who_are_inactive_with_transaction_paid() {
  	Notification::fake();
  	Event::fake();
  	$user = create('App\User');
  	$profile = create('App\Profile');
  	create('App\UserLocation', ['user_id' => $user->id, 'profile_id' => $profile->id, 'updated_at' => Carbon::now()->subMinutes(30)]);
  	$transaction = create('App\Transaction', ['user_id' => $user->id, 'profile_id' => $profile->id, 'paid' => true]);
  	$this->assertDatabaseHas('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  	Artisan::call('app:inactive_customer_manager');
  	$this->assertDatabaseMissing('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  }

  public function test_manager_sends_pay_or_keep_bill_open_notif_if_bill_not_closed_and_inactive() {
  	Notification::fake();
  	Event::fake();
  	$user = create('App\User');
  	$profile = create('App\Profile');
  	create('App\UserLocation', ['user_id' => $user->id, 'profile_id' => $profile->id, 'updated_at' => Carbon::now()->subMinutes(30)]);
  	$transaction = create('App\Transaction', ['user_id' => $user->id, 'profile_id' => $profile->id, 'paid' => false, 'bill_closed' => false]);
  	Artisan::call('app:inactive_customer_manager');
  	$this->assertDatabaseHas('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  	Notification::assertSentTo(
      [$user],
      PayOrKeepOpenNotification::class
    );
  }

  public function test_manager_sends_pay_or_keep_bill_open_notif_if_transaction_bill_was_closed_sent_and_inactive() {
  	Notification::fake();
  	Event::fake();
  	$user = create('App\User');
  	$profile = create('App\Profile');
  	create('App\UserLocation', ['user_id' => $user->id, 'profile_id' => $profile->id, 'updated_at' => Carbon::now()->subMinutes(30)]);
  	$transaction = create('App\Transaction', ['user_id' => $user->id, 'profile_id' => $profile->id, 'paid' => false, 'bill_closed' => false]);
  	$transaction->sendBillClosedNotification();
  	Artisan::call('app:inactive_customer_manager');
  	$this->assertDatabaseHas('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  	Notification::assertSentTo(
      [$user],
      PayOrKeepOpenNotification::class
    );
  }

  public function test_manager_charges_customer_if_pay_or_keep_open_sent_and_no_action_was_taken() {
  	Event::fake();
  	Mail::fake();
  	$user = create('App\User', ['default_tip_rate' => 20, 'customer_id' => env('TEST_SPLASH_CUSTOMER_ID')]);
  	create('App\PushToken', ['user_id' => $user->id]);
  	$photo = create('App\Photo');
    $profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $account = create('App\Account', ['profile_id' => $profile->id, 'splash_id' => env('TEST_SPLASH_MERCHANT_ID')]);
  	create('App\UserLocation', ['user_id' => $user->id, 'profile_id' => $profile->id, 'updated_at' => Carbon::now()->subMinutes(30)]);
  	$transaction = create('App\Transaction', ['user_id' => $user->id, 'profile_id' => $profile->id, 'paid' => false, 'bill_closed' => false]);
  	$transaction->sendPayOrKeepOpenNotification();

  	Artisan::call('app:inactive_customer_manager');
  	$this->assertDatabaseHas('transactions', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => true]);
  	$this->assertDatabaseMissing('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  }

  public function test_manager_sends_fix_bill_if_transaction_bill_was_previously_rejected() {
  	Notification::fake();
  	Event::fake();
  	$user = create('App\User');
  	$profile = create('App\Profile');
  	create('App\UserLocation', ['user_id' => $user->id, 'profile_id' => $profile->id, 'updated_at' => Carbon::now()->subMinutes(30)]);
  	$transaction = create('App\Transaction', ['user_id' => $user->id, 'profile_id' => $profile->id, 'paid' => false, 'bill_closed' => false, 'status' => 2]);
  	Artisan::call('app:inactive_customer_manager');
  	$this->assertDatabaseHas('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  	Notification::assertSentTo(
      [$user],
      FixTransactionNotification::class
    );
  }

  public function test_manager_sends_charges_customer_only_after_three_unresponded_to_fix_notifications() {
  	Event::fake();
  	Mail::fake();
  	$user = create('App\User', ['default_tip_rate' => 20, 'customer_id' => env('TEST_SPLASH_CUSTOMER_ID')]);
  	create('App\PushToken', ['user_id' => $user->id]);
  	$photo = create('App\Photo');
    $profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $account = create('App\Account', ['profile_id' => $profile->id, 'splash_id' => env('TEST_SPLASH_MERCHANT_ID')]);
  	create('App\UserLocation', ['user_id' => $user->id, 'profile_id' => $profile->id, 'updated_at' => Carbon::now()->subMinutes(30)]);
  	$transaction = create('App\Transaction', ['user_id' => $user->id, 'profile_id' => $profile->id, 'paid' => false, 'bill_closed' => false, 'status' => 2]);
  	Artisan::call('app:inactive_customer_manager');
  	$this->assertDatabaseHas('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  	$this->assertEquals(0, $transaction->fresh()->paid);

  	Artisan::call('app:inactive_customer_manager');
  	$this->assertDatabaseHas('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  	$this->assertEquals(0, $transaction->fresh()->paid);

  	Artisan::call('app:inactive_customer_manager');
  	$this->assertDatabaseHas('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  	$this->assertEquals(0, $transaction->fresh()->paid);

  	Artisan::call('app:inactive_customer_manager');
  	$this->assertDatabaseHas('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  	$this->assertEquals(0, $transaction->fresh()->paid);

  	Artisan::call('app:inactive_customer_manager');
  	$this->assertDatabaseMissing('user_locations', ['profile_id' => $profile->id, 'user_id' => $user->id]);
  	$this->assertEquals(1, $transaction->fresh()->paid);
  }
}
