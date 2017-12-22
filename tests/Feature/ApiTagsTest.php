<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTagsTest extends TestCase
{
	use RefreshDatabase;

	function test_a_mobile_user_can_retrieve_all_tags() {
		create('App\Tag', [], 10);

		$response = $this->get('/api/mobile/tags')->getData();
		$this->assertEquals(10, count($response->tags));
	}
}
