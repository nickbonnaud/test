<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConnectionTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_view_connections_show() {
		$this->withExceptionHandling();
  	$profile = create('App\Profile');
  	$user = create('App\User');

  	$this->get("/connections/{$profile->slug}")->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/connections/{$profile->slug}")->assertStatus(403);
	}

	function test_an_unauthorized_user_cannot_update_profile_connections() {
		$this->withExceptionHandling();
  	$profile = create('App\Profile');

  	$data = [
  		'action' => 'Enable',
  		'company' => 'facebook'
  	];

  	$this->patch("/api/web/connections/{$profile->slug}", $data)->assertRedirect('/login');
  	$this->signIn();
  	$this->patch("/api/web/connections/{$profile->slug}", $data)->assertStatus(403);
	}

	function test_an_authorized_user_can_enable_profile_connections_facebook() {
		$this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->id()]);

  	$data = [
  		'action' => 'Enable',
  		'company' => 'facebook'
  	];

  	$response = $this->patch("/api/web/connections/{$profile->slug}", $data)->getData();
  	$this->assertNotNull($response->url);
	}

	function test_an_authorized_user_can_enable_profile_connections_instagram() {
		$this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->id()]);

  	$data = [
  		'action' => 'Enable',
  		'company' => 'instagram'
  	];

  	$response = $this->patch("/api/web/connections/{$profile->slug}", $data)->getData();
  	$this->assertNotNull($response->url);
	}

	function test_an_authorized_user_can_enable_profile_connections_square() {
		$this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->id()]);
  	$account = create('App\Account', ['profile_id' => $profile->id]);

  	$data = [
  		'action' => 'Enable',
  		'company' => 'square'
  	];

  	$response = $this->patch("/api/web/connections/{$profile->slug}", $data)->getData();
  	$this->assertNotNull($response->url);
	}

}
