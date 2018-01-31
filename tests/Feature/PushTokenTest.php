<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\PushToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PushTokenTest extends TestCase
{
	use RefreshDatabase;

	function test_a_mobile_unathorized_user_cannot_post_a_push_token() {
		$this->withExceptionHandling();
		$user = create('App\User');

		$data = [
			'push_token' => "test_token",
			'device' => 'android'
		];

		$this->json('POST', 'api/mobile/push-token', $data)->assertStatus(401);
	}

	function test_a_mobile_user_can_post_a_new_push_token() {
		$user = create('App\User');

		$data = [
			'push_token' => "test_token",
			'device' => 'android'
		];

		$response = $this->json('POST', 'api/mobile/push-token', $data, $this->headers($user))->getData();
		$this->assertTrue($response->success);
		$this->assertDatabaseHas('push_tokens', ['user_id' => $user->id, 'device' => 'android', 'push_token' => "test_token"]);
	}

	function test_a_mobile_user_can_post_an_updated_push_token() {
		$user = create('App\User');
		$token = create('App\PushToken', ['user_id' => $user->id, 'push_token' => "test_token", 'device' => 'android']);

		$data = [
			'push_token' => "test_token",
			'device' => 'android'
		];

		$response = $this->json('POST', 'api/mobile/push-token', $data, $this->headers($user))->getData();
		$this->assertTrue($response->success);
		$this->assertDatabaseHas('push_tokens', ['user_id' => $user->id, 'device' => 'android', 'push_token' => "test_token"]);
		$this->assertEquals(1, PushToken::count());
	}

}
