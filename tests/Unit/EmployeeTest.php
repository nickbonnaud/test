<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeTest extends TestCase {
	use RefreshDatabase;

	function test_an_employee_belongs_to_a_profile() {
		$employee = create('App\Employee');
		$this->assertInstanceOf('App\Profile', $employee->profile);
	}

	function test_a_profile_can_have_multiple_employees() {
		$profile = create('App\Profile');
		create('App\Employee', ['profile_id' => $profile->id]);
		create('App\Employee', ['profile_id' => $profile->id]);

		$this->assertCount(2, $profile->employees);
	}
}