<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeTest extends TestCase
{
	use RefreshDatabase;

	function test_unauthorized_users_cannot_view_team_show_view() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');

		$this->get("/team/{$profile->slug}")->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/team/{$profile->slug}")->assertStatus(403);
	}

	function test_authorized_users_can_view_team_show_view() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id, 'tip_tracking_enabled' => true]);
		$notEmployee = create('App\User');
		$employeeOn = create('App\User', ['employer_id' => $profile->id, 'on_shift' => true, 'role' => 'employee']);
		$employeeOff = create('App\User', ['employer_id' => $profile->id, 'on_shift' => false, 'role' => 'employee']);

		$this->get("/team/{$profile->slug}")
			->assertSee($employeeOn->first_name)
			->assertSee($employeeOff->first_name)
			->assertDontSee($notEmployee->first_name);
	}

	function test_an_unauthorized_user_cannot_toggle_employee_shift() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$employeeOn = create('App\User', ['employer_id' => $profile->id, 'on_shift' => true, 'role' => 'employee']);

		$data = ['on_shift' => false];
		
		$this->patch("/api/web/users/{$profile->slug}/{$employeeOn->id}", $data)->assertRedirect('/login');
  	$this->signIn();
  	$this->patch("/api/web/users/{$profile->slug}/{$employeeOn->id}", $data)->assertStatus(403);
	}

	function test_an_authorized_user_can_toggle_employee_shift() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		$employeeOn = create('App\User', ['employer_id' => $profile->id, 'on_shift' => true, 'role' => 'employee']);
		$data = ['on_shift' => false];
		
		$response = $this->patch("/api/web/users/{$profile->slug}/{$employeeOn->id}", $data)->getData();
		$this->assertEquals($response->on_shift, false);
	}

	function test_an_unauthorized_user_cannot_search_users_by_email() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$user = create('App\User');

		$this->get("/api/web/users/{$profile->slug}/search?email={$user->email}")->assertRedirect('/login');
		$this->signIn();
		$this->get("/api/web/users/{$profile->slug}/search?email={$user->email}")->assertStatus(403);
	}

	function test_an_authorized_user_can_search_users_by_email() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		$user = create('App\User');

		$response = $this->get("/api/web/users/{$profile->slug}/search?email={$user->email}")->getData();
		$this->assertEquals($response->users[0]->id, $user->id);	
	}

	function test_an_authorized_user_can_search_users_by_first_last() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		$user = create('App\User');

		$response = $this->get("/api/web/users/{$profile->slug}/search?first={$user->first_name}&last={$user->last_name}")->getData();
		$this->assertEquals($response->users[0]->id, $user->id);	
	}

	function test_an_authorized_user_can_make_an_user_an_employee() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		$user = create('App\User');
		$data = ['employer_id' => $profile->id];
		
		$response = $this->patch("/api/web/users/{$profile->slug}/{$user->id}", $data)->getData();
		$this->assertEquals($response->employer_id, $profile->id);
	}

	function test_an_authorized_user_can_remove_an_employee() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		$user = create('App\User');
		$data = ['employer_id' => null];
		
		$response = $this->patch("/api/web/users/{$profile->slug}/{$user->id}", $data)->getData();
		$this->assertEquals($response->employer_id, null);
	}

	function test_an_authorized_user_can_retrieve_employees_on_shift() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		$employeeOn = create('App\User', ['employer_id' => $profile->id, 'on_shift' => true, 'role' => 'employee']);
		$employeeOff = create('App\User', ['employer_id' => $profile->id, 'on_shift' => false, 'role' => 'employee']);

		$response = $this->get("/api/web/users/{$profile->slug}/search?dashboard=1&onShift=1")->getData();
		$this->assertEquals($response->users[0]->id, $employeeOn->id);
	}
}
