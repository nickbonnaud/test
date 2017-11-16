<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostAnalyticsTest extends TestCase
{
	use RefreshDatabase;

	function test_a_postAnalytics_belongs_to_a_profile() {
    $postAnalytic = create('App\PostAnalytics');
    $this->assertInstanceOf('App\Profile', $postAnalytic->profile);
  }

  function test_a_postAnalytics_belongs_to_a_user() {
    $postAnalytic = create('App\PostAnalytics');
    $this->assertInstanceOf('App\User', $postAnalytic->user);
  }

  function test_a_postAnalytics_belongs_to_a_post() {
    $postAnalytic = create('App\PostAnalytics');
    $this->assertInstanceOf('App\Post', $postAnalytic->post);
  }

  function test_postAnalytics_saves_viewed_on_when_saving() {
  	$postAnalytic = create('App\PostAnalytics', ['viewed' => true]);
  	$this->assertNotEquals($postAnalytic->viewed_on, null);
  }

  function test_postAnalytics_saves_shared_on_when_saving() {
  	$postAnalytic = create('App\PostAnalytics', ['shared' => true]);
  	$this->assertNotEquals($postAnalytic->shared_on, null);
  }

  function test_postAnalytics_saves_bookmarked_on_when_saving() {
  	$postAnalytic = create('App\PostAnalytics', ['bookmarked' => true]);
  	$this->assertNotEquals($postAnalytic->bookmarked_on, null);
  }

  function test_postAnalytics_saves_transaction_on_when_saving() {
    $postAnalytic = create('App\PostAnalytics', ['transaction_resulted' => true]);
    $this->assertNotEquals($postAnalytic->transaction_on, null);
  }

  function test_postAnalytics_saves_viewed_on_when_updating_shared() {
    $postAnalytic = create('App\PostAnalytics', ['shared' => true]);
    $this->assertNotEquals($postAnalytic->viewed, null);
    $this->assertNotEquals($postAnalytic->viewed_on, null);
  }

  function test_postAnalytics_saves_viewed_on_when_updating_bookmarked() {
    $postAnalytic = create('App\PostAnalytics', ['bookmarked' => true]);
    $this->assertNotEquals($postAnalytic->viewed, null);
    $this->assertNotEquals($postAnalytic->viewed_on, null);
  }
}
