<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiLoyaltyCardTest extends TestCase
{
	use RefreshDatabase;

	function test_unauthorized_users_cannot_retrieve_users_loyalty_cards() {
		$this->withExceptionHandling();
		$user = create('App\User');
		$profile = create('App\Profile');
		$loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id]);

		$loyaltyCard = create('App\LoyaltyCard', ['loyalty_program_id' => $loyaltyProgram->id, 'user_id' => $user->id]);

		$this->json('GET', '/api/mobile/loyalty')->assertStatus(401);
	}

	function test_a_user_not_owning_loyalty_cards_cannot_retrieve_loyalty_cards() {
		$user = create('App\User');
		$unAuthorizedUser = create('App\User');
		$profile = create('App\Profile');
		$loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id]);

		$loyaltyCard = create('App\LoyaltyCard', ['loyalty_program_id' => $loyaltyProgram->id, 'user_id' => $user->id]);

		$response = $this->get('/api/mobile/loyalty', $this->headers($unAuthorizedUser))->getData();
		$this->assertCount(0, $response->data);
	}

	function test_a_user_owning_loyalty_cards_can_retrieve_loyalty_cards() {
		$user = create('App\User');
		$logo = create('App\Photo');
		$profile = create('App\Profile', ['logo_photo_id' => $logo->id]);
		$profileOne = create('App\Profile', ['logo_photo_id' => $logo->id]);

		$loyaltyProgram = create('App\LoyaltyProgram', ['profile_id' => $profile->id]);
		$loyaltyProgramOne = create('App\LoyaltyProgram', ['profile_id' => $profileOne->id]);

		$loyaltyCard = create('App\LoyaltyCard', ['loyalty_program_id' => $loyaltyProgram->id, 'user_id' => $user->id]);
		$loyaltyCardOne = create('App\LoyaltyCard', ['loyalty_program_id' => $loyaltyProgramOne->id, 'user_id' => $user->id]);

		$anotherUser = create('App\User');
		$loyaltyCardAnother = create('App\LoyaltyCard', ['loyalty_program_id' => $loyaltyProgram->id, 'user_id' => $anotherUser->id]);

		$response = $this->get('/api/mobile/loyalty', $this->headers($user))->getData();
		$this->assertCount(2, $response->data);
	}
}
