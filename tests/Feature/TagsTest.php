<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TagsTest extends TestCase
{
  use DatabaseMigrations;

  function test_an_unauthorized_user_cannot_update_tags() {
  	$this->withExceptionHandling();
  	$tag = create('App\Tag');
  	$profile = create('App\Profile');

  	$data = [
  		'tags' => [
  			0 => $tag->id,
  		]
  	];
  	$this->patch("tags/{$profile->slug}", $data)->assertRedirect('/login');
  	$this->signIn();
    $this->patch("tags/{$profile->slug}", $data)->assertStatus(403);
  }

  function test_an_authorized_user_can_update_their_profiles_tags() {
  	$this->signIn();
  	$tag = create('App\Tag');
  	$profile = create('App\Profile', ['user_id' => auth()->id()]);
    create('App\GeoLocation', ['profile_id' => $profile->id, 'identifier' => $profile->business_name,]);

  	$data = [
  		'tags' => [
  			0 => $tag->id,
  		]
  	];

  	$this->patch("tags/{$profile->slug}", $data)->assertRedirect("/profiles/{$profile->slug}/edit");
  	$this->assertDatabaseHas('profile_tag', ['profile_id' => $profile->id, 'tag_id' => $tag->id]);
  }
}
