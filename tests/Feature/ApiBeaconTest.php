<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiBeaconTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_create_retrieve_a_beacon() {
		$this->withExceptionHandling();
		$user = create('App\User');
		$profile = create('App\Profile');
		$beacon = create('App\Beacon');

		$response = $this->get('/api/mobile/transactions?recent=1')->assertStatus(401);
	}

	function test_an_authorized_user_can_retrieve_a_beacon_that_was_aleady_made() {
		$user = create('App\User');
		$profile = create('App\Profile', ['user_id' => $user->id]);
		$beacon = create('App\Beacon', ['profile_id' => $profile->id]);
		$response = $this->get('/api/mobile/pay/beacons', $this->headers($user))->getData();
		
		$this->assertEquals($beacon->uuid, $response->data->uuid);
		$this->assertEquals($beacon->identifier, $response->data->identifier);
	}

	function test_an_authorized_user_can_create_a_new_beacon() {
		$user = create('App\User');
		$profile = create('App\Profile', ['user_id' => $user->id]);

		$response = $this->get('/api/mobile/pay/beacons', $this->headers($user))->getData();

		$this->assertEquals($profile->slug, $response->data->identifier);
		$this->assertDatabaseHas('beacons', ['profile_id' => $profile->id, 'identifier' => $profile->slug]);
		$this->assertEquals($profile->beacon->uuid, $response->data->uuid);
	}
}
