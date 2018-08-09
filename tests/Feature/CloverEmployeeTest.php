<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CloverEmployeeTest extends TestCase {
	use RefreshDatabase;

	function test_an_unauthorized_clover_client_cannot_retrieve_employees() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$employeeOne = create('App\Employee', ['profile_id' => $profile->id]);
		$employeeTwo = create('App\Employee', ['profile_id' => $profile->id]);

		$this->get("/api/mobile/pay/employees")->assertStatus(401);
	}

	function test_a_client_not_owning_employees_cannot_retrieve_employees() {
		$user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
		$employeeOwned = create('App\Employee', ['profile_id' => $profile->id]);

		$profileTwo = create('App\Profile');
		$employeeNotOwned= create('App\Employee', ['profile_id' => $profileTwo->id]);

		$response = $this->get("/api/mobile/pay/employees", $this->headers($user))->getData();
		$this->assertCount(1, $response->data);
		$this->assertEquals($employeeOwned->id, $response->data[0]->id);
	}

	function test_an_authorized_clover_client_can_retrieve_employees() {
		$user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
		$employeeOne = create('App\Employee', ['profile_id' => $profile->id]);
		$employeeTwo = create('App\Employee', ['profile_id' => $profile->id]);
		$employeeThree = create('App\Employee', ['profile_id' => $profile->id]);

		$response = $this->get("/api/mobile/pay/employees", $this->headers($user))->getData();
		$this->assertCount(3, $response->data);
	}

	function test_an_unauthorized_clover_client_cannot_add_delete_employees() {
		$this->withExceptionHandling();
		$user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);

  	$data = [
  		'pos_employee_id' => 'employee_id',
  		'name' => 'test employee',
  		'role' => 'manager',
  		'is_create' => 'true'
  	];

  	$response = $this->post("/api/mobile/pay/employees", $data)->assertStatus(401);
	}

	function test_an_authorized_clover_client_can_add_an_employees() {
		$this->withExceptionHandling();
		$user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);

  	$data = [
  		'pos_employee_id' => 'employee_id',
  		'name' => 'test employee',
  		'role' => 'manager',
  		'is_create' => true
  	];

  	$this->assertDatabaseMissing('employees', ['pos_employee_id' => 'employee_id']);
  	$response = $this->post("/api/mobile/pay/employees", $data, $this->headers($user))->getData();
  	$this->assertEquals('employees_updated', $response->success);
  	$this->assertDatabaseHas('employees', ['pos_employee_id' => 'employee_id']);
	}

	function test_an_authorized_clover_client_can_remove_an_employees() {
		$this->withExceptionHandling();
		$user = create('App\User');
  	$profile = create('App\Profile', ['user_id' => $user->id]);
  	$employee = create('App\Employee', [
  		'profile_id' => $profile->id,
  		'pos_employee_id' => 'employee_id',
  		'name' => 'test employee',
  		'role' => 'manager'
  	]);
  	

  	$data = [
  		'pos_employee_id' => 'employee_id',
  		'name' => 'test employee',
  		'role' => 'manager',
  		'is_create' => false
  	];

  	$this->assertDatabaseHas('employees', ['pos_employee_id' => 'employee_id']);
  	$response = $this->post("/api/mobile/pay/employees", $data, $this->headers($user))->getData();
  	$this->assertEquals('employees_updated', $response->success);
  	$this->assertDatabaseMissing('employees', ['pos_employee_id' => 'employee_id']);
	}
}