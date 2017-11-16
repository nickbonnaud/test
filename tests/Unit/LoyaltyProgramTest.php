<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoyaltyProgramTest extends TestCase
{
	use RefreshDatabase;

	function test_a_loyalty_program_belongs_to_a_profile() {
		$program = create('App\LoyaltyProgram');
    $this->assertInstanceOf('App\Profile', $program->profile);
	}

	function test_a_loyalty_program_stores_amount_required_in_correct_format() {
		$program = create('App\LoyaltyProgram', ['amount_required' => '$ 7.99']);
  	$this->assertDatabaseHas('loyalty_programs', ['amount_required' => 799]);
	}

	function test_a_loyalty_program_gets_amount_required_to_correct_format() {
   	$program = create('App\LoyaltyProgram', ['amount_required' => '$ 7.99']);
    $this->assertEquals($program->amount_required, 7.99);
  }

  function test_a_loyalty_program_stores_reward_in_lowercase() {
		$program = create('App\LoyaltyProgram', ['reward' => 'Coffee']);
  	$this->assertDatabaseHas('loyalty_programs', ['reward' => 'coffee']);
	}
}
