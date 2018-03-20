<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BeaconTest extends TestCase
{
	use RefreshDatabase;

	function test_a_beacon_belongs_to_a_profile() {
		$beacon = create('App\Beacon');
		$this->assertInstanceOf('App\Profile', $beacon->profile);
	}

	function test_a_saves_identifier_as_profile_slug() {
		$beacon = create('App\Beacon');
		$this->assertEquals($beacon->identifier, $beacon->profile->slug);
	}
}
