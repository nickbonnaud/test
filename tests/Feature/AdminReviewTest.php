<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use App\Events\AccountReadyForProcessorReview;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminReviewTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_view_review_page() {
  	$this->withExceptionHandling();
		$profile = create('App\Profile');

		$this->get("/business/review")->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/business/review")->assertStatus(403);
  }

  function test_an_admin_user_can_view_review_page() {
  	$user = create('App\User', ['is_admin' => true]);
  	$this->signIn($user);
  	$profile = create('App\Profile', ['user_id' => $user->id]);

  	$this->get("/business/review")->assertSee('Businesses Pending Review');
  }

  function test_an_unauthorized_user_cannot_approve_a_profile() {
  	$this->withExceptionHandling();
		$profile = create('App\Profile');

		$data = [
			'approved' => true
		];

		$this->patch("/business/review/profile/{$profile->slug}", $data)->assertRedirect('/login');
  	$this->signIn();
  	$this->patch("/business/review/profile/{$profile->slug}", $data)->assertStatus(403);
  }

  function test_an_authorized_user_can_approve_a_profile() {
  	$user = create('App\User', ['is_admin' => true]);
  	$this->signIn($user);
  	$profile = create('App\Profile');

  	$data = [
			'approved' => true
		];

		$this->patch("/business/review/profile/{$profile->slug}", $data);
		$this->assertTrue($profile->fresh()->approved);
  }

  function test_an_unauthorized_user_cannot_approve_an_account() {
  	$this->withExceptionHandling();
		$profile = create('App\Profile');
		$account = create('App\Account', ['profile_id' => $profile->id]);

		$data = [
			'status' => "pending"
		];

		$this->patch("/business/review/account/{$account->slug}", $data)->assertRedirect('/login');
  	$this->signIn();
  	$this->patch("/business/review/account/{$account->slug}", $data)->assertStatus(403);
  }

  function test_an_authorized_user_can_approve_an_account() {
  	$this->expectsEvents(AccountReadyForProcessorReview::class);
  	$user = create('App\User', ['is_admin' => true]);
  	$this->signIn($user);
  	$profile = create('App\Profile');
  	$account = create('App\Account', ['profile_id' => $profile->id]);

  	$data = [
			'status' => 'pending'
		];

		$this->patch("/business/review/account/{$account->slug}", $data);
		$this->assertEquals('pending', $account->fresh()->status);
  }
}
