<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoyaltyProgramTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_view_create_loyalty_program() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');

		$this->get("loyalty-program/{$profile->slug}/create")->assertRedirect('/login');
  	$this->signIn();
  	$this->get("loyalty-program/{$profile->slug}/create")->assertStatus(403);
	}

	function test_an_authorized_user_can_view_create_loyalty_program() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
  	$this->get("loyalty-program/{$profile->slug}/create")->assertSee('Create your Customer Loyalty Program');
	}

	function test_an_unauthorized_user_cannot_store_loyalty_program() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$program = make('App\LoyaltyProgram', [
			'optionsRadios' => 'increments',
			'purchases_required' => 10
		]);

		$this->post("loyalty-program/{$profile->slug}", $program->toArray())->assertRedirect('/login');
  	$this->signIn();
  	$this->post("loyalty-program/{$profile->slug}", $program->toArray())->assertStatus(403);
	}

	function test_an_authorized_user_can_store_loyalty_program() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		$data =  [
			'reward' => 'Coffee',
			'optionsRadios' => 'increments',
			'purchases_required' => 10
		];

  	$response = $this->post("loyalty-program/{$profile->slug}", $data);
  	$this->assertDatabaseHas('loyalty_programs', ['profile_id' => $profile->id, 'purchases_required' => 10, 'is_increment' => true]);
  	$this->get($response->headers->get('Location'))
      ->assertSee('coffee');
	}

	function test_an_unauthorized_user_cannot_delete_a_loyalty_program() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$program = create('App\LoyaltyProgram', [
			'is_increment' => true,
			'purchases_required' => 10
		]);

		$this->delete("loyalty-program/{$profile->slug}")->assertRedirect('/login');
  	$this->signIn();
  	$this->delete("loyalty-program/{$profile->slug}")->assertStatus(403);
	}

	function test_an_authorized_user_can_delete_a_loyalty_program() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
		$program = create('App\LoyaltyProgram', [
			'profile_id' => $profile->id,
			'is_increment' => true,
			'purchases_required' => 10
		]);
		$loyaltyCardOne = create('App\LoyaltyCard', ['loyalty_program_id' => $program->id, 'current_amount' => 2]);
		$loyaltyCardTwo = create('App\LoyaltyCard', ['loyalty_program_id' => $program->id, 'current_amount' => 5]);

  	$response = $this->delete("loyalty-program/{$profile->slug}");

  	$this->assertDatabaseMissing('loyalty_programs', ['id' => $program->id]);
  	$this->assertDatabaseMissing('loyalty_cards', ['id' => $loyaltyCardOne->id, 'id' => $loyaltyCardTwo->id]);
  	$this->get($response->headers->get('Location'))
      ->assertDontSee($program->reward)
      ->assertSee('Create your Customer Loyalty Program');;
	}
}
