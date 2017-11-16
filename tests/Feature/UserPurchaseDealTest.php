<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPurchaseDealTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_purchase_a_deal() {
		$this->withExceptionHandling();
  	$profile = create('App\Profile');
  	$user = create('App\User');
  	$post = create('App\Post', ['is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 300]);

  	$data = [
  		'deal_id' => $post->id
  	];

  	$this->post("/api/mobile/transactions/{$profile->slug}", $data)->assertStatus(401);
	}

	function test_an_authorized_user_can_purchase_a_deal() {
  	$tax = create('App\Tax');
    $profile = create('App\Profile', ['tax_id' => $tax->id]);
  	$user = create('App\User');
  	$post = create('App\Post', ['is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 300]);

  	$data = [
  		'deal_id' => $post->id
  	];

  	$this->post("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user));
  	$tax = round(($profile->tax->total / 10000) * $post->price);
  	$total = $tax + $post->price;
  	$this->assertDatabaseHas('transactions', ['user_id' => $user->id, 'profile_id' => $profile->id, 'deal_id' => $post->id, 'tax' => $tax, 'net_sales' => $post->price, 'total' => $total]);
	}
}
