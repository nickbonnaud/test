<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiNotificationTest extends TestCase
{
	use RefreshDatabase;

	function test_a_user_cannot_retrieve_bill_notifications_without_a_token() {
  	$photo = create('App\Photo');
		$profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
  	$user = create('App\User');
  	create('App\PushToken', ['user_id' => $user->id]);
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

  	$response = $this->get("/api/mobile/notifications")->getData();

  	$this->assertEquals('token_absent', $response->error);
	}

	function test_an_authorized_user_can_retrieve_bill_notifications() {
  	$photo = create('App\Photo');
		$profile = create('App\Profile', ['logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
  	$user = create('App\User');
  	create('App\PushToken', ['user_id' => $user->id]);
  	$transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'bill_closed' => true, 'is_refund' => false]);

  	$response = $this->get("/api/mobile/notifications", $this->headers($user))->getData();
  	$this->assertEquals($transaction->id, $response->open_bill->transactionId);
  	$this->assertEquals($profile->id, $response->open_bill->businessId);
	}
}
