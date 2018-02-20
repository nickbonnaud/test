<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Mail\TransactionErrorEmail;
use Illuminate\Support\Facades\Mail;
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
    $account = create('App\Account', ['profile_id' => $profile->id, 'splash_id' => 't1_mer_5a10dfa09f4b06f5e326d8b']);
  	$user = create('App\User', ['customer_id' => 'c793a464b8d3085506e1e82378656dbb']);
  	$post = create('App\Post', ['is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 5]);
  	$data = [
  		'deal_id' => $post->id
  	];

  	$response = $this->post("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user))->getData();
    $this->assertEquals(true, $response->success);
    $this->assertEquals('user_deal', $response->type);
  	$tax = round(($profile->tax->total / 10000) * $post->price);
  	$total = $tax + $post->price;
  	$this->assertDatabaseHas('transactions', ['user_id' => $user->id, 'profile_id' => $profile->id, 'deal_id' => $post->id, 'tax' => $tax, 'net_sales' => $post->price, 'total' => $total, 'paid' => true]);
	}

  function test_a_purchased_deal_that_fails_sends_fail_email_to_admin() {
    Mail::fake();
    $tax = create('App\Tax');
    $profile = create('App\Profile', ['tax_id' => $tax->id]);
    $account = create('App\Account', ['profile_id' => $profile->id, 'splash_id' => 't1_mer_5a10dfa09f4b06f5e326d8b']);
    $user = create('App\User', ['customer_id' => 'c793a464b8d3085506e1e82378656db']);
    $post = create('App\Post', ['is_redeemable' => true, 'deal_item' => 'Coffee', 'price' => 300]);

    $data = [
      'deal_id' => $post->id
    ];

    $response = $this->post("/api/mobile/transactions/{$profile->slug}", $data, $this->headers($user))->getData();
    $this->assertEquals(false, $response->success);
    $this->assertEquals('user_deal', $response->type);
    $this->assertDatabaseHas('transactions', ['user_id' => $user->id, 'profile_id' => $profile->id, 'deal_id' => $post->id, 'paid' => false]);
    Mail::assertSent(TransactionErrorEmail::class, function($mail) use ($profile, $user) {
      return  $mail->profile->id == $profile->id &&
              $mail->user->id == $user->id &&
              $mail->hasTo(env('DEFAULT_EMAIL'));
    });
  }
}
