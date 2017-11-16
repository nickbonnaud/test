<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CityTest extends TestCase
{
  use RefreshDatabase;

  function test_a_city_has_many_profiles() {
    $city = create('App\City');
    $profile = create('App\Profile', ['city_id' => $city->id]);
    $profileTwo = create('App\Profile', ['city_id' => $city->id]);

    $this->assertCount(2, $city->profiles);
  }

  function test_a_city_can_create_a_slug() {
    $city = create('App\City');
    $this->assertEquals($city->slug, str_slug($city->name, '-'));
  }

  function test_a_city_slug_is_unique() {
    $cityFirst = create('App\City');
    $citySecond = make('App\City', ['name' => $cityFirst->name]);
    $this->assertNotEquals($cityFirst->slug, $citySecond->slug);
  }

  function test_a_city_transforms_data_to_lowercase() {
    $city = create('App\City', ['name' => 'Raleigh', 'county' => 'Wake', 'state' => 'NC']);
    $this->assertEquals(strtolower('Raleigh'), $city->name);
    $this->assertEquals(strtolower('Wake'), $city->county);
    $this->assertEquals(strtolower('NC'), $city->state);
  }
}
