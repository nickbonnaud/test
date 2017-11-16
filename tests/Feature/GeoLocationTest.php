<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class GeoLocationTest extends TestCase
{
  use DatabaseMigrations;

  function test_an_unauthorized_user_cannot_update_a_geoLocation() {
  	$this->withExceptionHandling();
  	$geoLocation = create('App\GeoLocation');

  	$data = [
  		'latitude' => 34.78172123,
  		'longitude' => -78.65666912,
      'state' => 'NC',
      'county' => 'Wake',
      'zip' => 27603
  	];
  	$this->patch($geoLocation->path(), $data)->assertRedirect('/login');
  	$this->signIn();
    $this->patch($geoLocation->path(), $data)->assertStatus(403);
  }

  function test_an_authorized_user_can_update_a_geoLocation() {
  	$this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->id()]);
  	$geoLocation = create('App\GeoLocation', ['profile_id' => $profile->id, 'identifier' => $profile->business_name]);

  	$lat = rand(-90, 90) + .78172123;
  	$lng = rand(-180, 180) + .65666912;
  	$data = [
  		'latitude' => $lat,
  		'longitude' => $lng,
      'state' => 'NC',
      'county' => 'Wake',
      'zip' => 27603
  	];
    $this->patch($geoLocation->path(), $data)->assertRedirect("/profiles/{$geoLocation->profile->slug}/edit");
  	$this->assertDatabaseHas('geo_locations', ['latitude' => $lat, 'longitude' => $lng]);
  }
}
