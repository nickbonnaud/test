<?php

namespace Tests\Feature;

use App\Transaction;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionsChangeEventFires extends TestCase
{
	use RefreshDatabase;

	function test_transaction_change_event_fires_when_new_bill_is_closed() {
    $this->signIn();
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['user_id' => auth()->id(), 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $user = create('App\User');
    create('App\PushToken', ['user_id' => $user->id]);
    
    $data = [
      'user_id' => $user->id,
      'profile_id' => $profile->id,
      'products' => 'pizza',
      'tax' => 100,
      'net_sales' => 200,
      'total' => 300,
      'bill_closed' => true
    ];

    $this->post("/api/web/transactions/{$profile->slug}/{$user->id}", $data);
    $this->assertDatabaseHas('transactions', ['user_id' => $user->id, 'profile_id' => $profile->id, 'status' => 11]);
	}

	function test_transaction_change_event_fires_when_open_bill_is_closed() {
    $this->signIn();
    $photo = create('App\Photo');
    $profile = create('App\Profile', ['user_id' => auth()->id(), 'logo_photo_id' => $photo->id, 'hero_photo_id' => $photo->id]);
    $user = create('App\User');
    create('App\PushToken', ['user_id' => $user->id]);
    
    $data = [
      'user_id' => $user->id,
      'profile_id' => $profile->id,
      'products' => 'pizza',
      'tax' => 100,
      'net_sales' => 200,
      'total' => 300,
    ];

    $this->post("/api/web/transactions/{$profile->slug}/{$user->id}", $data);
    $this->assertDatabaseHas('transactions', ['user_id' => $user->id, 'profile_id' => $profile->id, 'status' => null]);
    $data = [
    	'bill_closed' => true
    ];
    $transaction = Transaction::first();
    $this->patch("/api/web/transactions/{$profile->slug}/{$transaction->id}", $data);
    $this->assertDatabaseHas('transactions', ['user_id' => $user->id, 'profile_id' => $profile->id, 'status' => 11]);
	}
}
