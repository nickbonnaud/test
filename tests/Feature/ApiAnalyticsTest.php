<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiAnalyticsTest extends TestCase
{
  
	use RefreshDatabase;

  function test_mobile_users_can_update_post_analytics_not_registered() {
  	$profileOne = create('App\Profile');
  	$postOne = create('App\Post', ['profile_id' => $profileOne->id]);

  	$profileTwo = create('App\Profile');
  	$postTwo= create('App\Post', ['profile_id' => $profileTwo->id, 'views' => 4]);

  	$data = [
  		[
  			'post_id' => $postOne->id,
  			'user_id' => null,
  			'profile_id' => $profileOne->id,
  			'viewed' => true,
  			'viewed_on' =>  '2018-03-13T00:21:24-04:00'
  		],
  		[
  			'post_id' => $postTwo->id,
  			'user_id' => null,
  			'profile_id' => $profileTwo->id,
  			'viewed' => true,
  			'viewed_on' => '2018-03-13T00:21:24-04:00',
  			'bookmarked' => true,
  			'bookmarked_on' => '2018-03-13T00:21:24-04:00',
  		]
  	];

  	$response = $this->json('POST', "api/mobile/analytics/posts", ['analytics' => $data])->getData();
  	$this->assertTrue($response->success);
    $this->assertDatabaseHas('posts', ['id' => $postOne->id, 'views' => 1]);
    $this->assertDatabaseHas('posts', ['id' => $postTwo->id, 'views' => 5, 'bookmarks' => 1]);
  }

  function test_mobile_users_can_update_post_analytics_registered_no_previous() {
    $user = create('App\User');
    $profileOne = create('App\Profile');
    $postOne = create('App\Post', ['profile_id' => $profileOne->id]);

    $profileTwo = create('App\Profile');
    $postTwo= create('App\Post', ['profile_id' => $profileTwo->id, 'views' => 4]);

    $data = [
      [
        'post_id' => $postOne->id,
        'user_id' => $user->id,
        'profile_id' => $profileOne->id,
        'viewed' => true,
        'viewed_on' =>  '2018-03-13T00:21:24-04:00'
      ],
      [
        'post_id' => $postTwo->id,
        'user_id' => $user->id,
        'profile_id' => $profileTwo->id,
        'viewed' => true,
        'viewed_on' => '2018-03-13T00:21:24-04:00',
        'bookmarked' => true,
        'bookmarked_on' => '2018-03-13T00:21:24-04:00',
      ]
    ];

    $response = $this->json('POST', "api/mobile/analytics/posts", ['analytics' => $data])->getData();
    $this->assertTrue($response->success);
    $this->assertDatabaseHas('posts', ['id' => $postOne->id, 'views' => 1]);
    $this->assertDatabaseHas('posts', ['id' => $postTwo->id, 'views' => 5, 'bookmarks' => 1]);

    $this->assertDatabaseHas('post_analytics', ['user_id' => $user->id, 'post_id' => $postOne->id, 'profile_id' => $profileOne->id, 'viewed' => true]);
    $this->assertDatabaseHas('post_analytics', ['user_id' => $user->id, 'post_id' => $postTwo->id, 'profile_id' => $profileTwo->id, 'viewed' => true, 'bookmarked' => true]);
  }

  function test_mobile_users_can_update_post_analytics_registered_with_previous() {
    $user = create('App\User');
    $profileOne = create('App\Profile');
    $postOne = create('App\Post', ['profile_id' => $profileOne->id, 'views' => 1]);
    $postAnalytic = create('App\PostAnalytics', ['post_id' => $postOne->id, 'user_id' => $user->id, 'profile_id' => $profileOne->id, 'viewed' => true, 'viewed_on' => Carbon::now()]);

    $data = [
      [
        'post_id' => $postOne->id,
        'user_id' => $user->id,
        'profile_id' => $profileOne->id,
        'viewed' => true,
        'viewed_on' => '2018-03-13T00:21:24-04:00',
        'shared' => true,
        'shared_on' => '2018-03-13T00:21:24-04:00'
      ]
    ];

    $response = $this->json('POST', "api/mobile/analytics/posts", ['analytics' => $data])->getData();
    $this->assertTrue($response->success);
    $this->assertDatabaseHas('posts', ['id' => $postOne->id, 'views' => 2, 'shares' => 1]);

    $this->assertDatabaseHas('post_analytics', ['user_id' => $user->id, 'post_id' => $postOne->id, 'profile_id' => $profileOne->id, 'viewed' => true, 'shared' => true]);
  }
}
