<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase {
	use RefreshDatabase;

	public function setUp() {
  	parent::setUp();
  }

  function test_a_post_belongs_to_a_profile() {
  	$post = create('App\Post');
  	$this->assertInstanceOf('App\Profile', $post->profile);
  }

  function test_a_post_sets_published_at_attribute() {
  	$post = make('App\Post');
  	$post->save();

  	$this->assertNotEquals($post->published_at, null);
  }

  function test_a_post_formats_for_html_body_and_message() {
  	$post = create('App\Post', [
  		'message' => 
		  	'This is a line.

		  	This is another line.
		  	Final line',
		  'body' => 
		  	'This is a line.

		  	This is another line.
		  	Final line',
	  ]);

  	$this->assertNotContains("\n", $post->formatted_body);
  	$this->assertNotContains("\n", $post->formatted_message);
  }

  function test_a_post_sets_price_to_correct_format() {
    $post = create('App\Post', [
      'message' => "Fake Test Deal",
      'deal_item' => 'Coffee',
      'price' => '$ 9.99',
      'end_date' => date("Y-m-d")
    ]);
    $this->assertDatabaseHas('posts', ['price' => 999]);
  }

  function test_a_post_gets_price_to_correct_format() {
    $post = create('App\Post', [
      'message' => "Fake Test Deal",
      'deal_item' => 'Coffee',
      'price' => '$ 9.99',
      'end_date' => date("Y-m-d")
    ]);
    $this->assertEquals($post->price, 9.99);
  }

  function test_a_post_that_is_a_deal_sets_is_redeemable_to_true() {
    $post = create('App\Post', [
      'message' => "Fake Test Deal",
      'deal_item' => 'Coffee',
      'price' => '$ 9.99',
      'end_date' => date("Y-m-d")
    ]);
    $this->assertEquals($post->is_redeemable, true);
  }
}
