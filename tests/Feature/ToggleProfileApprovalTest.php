<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ToggleProfileApprovalTest extends TestCase
{
	use RefreshDatabase;

	function test_unauthorized_user_cannot_toggle_profile_approval() {
		$data = ["type" => 'profile'];
		$profile = create('App\Profile');
		$this->post($profile->path() . '/approve', $data)->assertRedirect('/login');

		$this->signIn();
		$this->post($profile->path() . '/approve', $data)->assertStatus(403);
	}

	function test_unauthorized_user_who_owns_profile_cannot_toggle_profile_approval() {
		$data = ["type" => 'profile'];
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);

		$this->post($profile->path() . '/approve', $data)->assertStatus(403);		
	}

	function test_admin_authorized_to_approve_profile() {
		$data = ["type" => 'profile'];
		$profile = create('App\Profile');
		$user = create('App\User', ['is_admin' => true]);
		$user2 = create('App\User');

		$this->actingAs($user)->post($profile->path() . '/approve', $data);
		$this->assertDatabaseHas('profiles', ['id' => $profile->id, 'approved' => true]);
	}

	function test_admin_authorized_to_unapprove_profile() {
		$data = ["type" => 'profile'];
		$profile = create('App\Profile', ['approved' => true]);
		$user = create('App\User', ['is_admin' => true]);
		$user2 = create('App\User');

		$this->actingAs($user)->post($profile->path() . '/unapprove', $data);
		$this->assertDatabaseHas('profiles', ['id' => $profile->id, 'approved' => false]);
	}
}
