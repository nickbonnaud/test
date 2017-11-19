<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiCardTest extends TestCase
{
  use RefreshDatabase;


  public function test_an_unauthorized_user_cannot_view_payment_page() {
  	$this->withExceptionHandling();
  	$this->json('GET', '/api/mobile/card/vault')->assertStatus(401);
  }

  public function test_an_authorized_user_can_view_payment_page() {
  	$user = create('App\User');
  	$this->get('/api/mobile/card/vault', $this->headers($user))->assertSee('Loading...');
  }

  public function test_an_authorized_user_can_send_their_token_data_to_pockeyt() {
  	$user = create('App\User');
  	$data = [
  		'token' => 'exampleToken',
      'numberLastFour' => '1223',
      'cardType' => '3',
  	];
  	$response = $this->post("api/mobile/card/vault/{$user->id}", $data, $this->headers($user))->getData();
  	$this->assertTrue($response);
  	$this->assertEquals('MASTERCARD', $user->fresh()->card_type);
  	$this->assertEquals('exampleToken', $user->fresh()->customer_id);
  	$this->assertEquals('1223', $user->fresh()->last_four_card);
  }
}
