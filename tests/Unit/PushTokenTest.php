<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PushTokenTest extends TestCase
{
  use RefreshDatabase;

  function test_a_push_token_belongs_to_a_user() {
  	$token = create('App\PushToken');
  	$this->assertInstanceOf('App\User', $token->user);
  }

  function test_a_user_has_one_push_token() {
  	$user = create('App\User');
  	$token = create('App\PushToken', ['user_id' => $user->id]);
  	$this->assertInstanceOf('App\PushToken', $user->pushToken);
  }
}
