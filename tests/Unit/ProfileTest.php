<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\City;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
  use RefreshDatabase;

  function test_a_profile_has_an_owner() {
  	$this->assertInstanceOf('App\User', $this->profile->user);
  }

  function test_a_profile_can_check_its_website_url_for_missing_http() {
  	$profile = create('App\Profile', ['website' => 'www.acme.com']);
  	$this->assertEquals($profile->website, "http://www.acme.com");
  }

  function test_a_profile_can_check_its_website_url_for_missing_www() {
  	$profile = create('App\Profile', ['website' => 'acme.com']);
  	$this->assertEquals($profile->website, "http://www.acme.com");
  }

  function test_a_profile_can_add_tax_rate() {
  	$profile = create('App\Profile');
  	$tax = create('App\Tax');

  	$profile = $profile->addTaxRate('wake county', 'nc');
  	
  	$this->assertEquals($profile->tax_id, $tax->id);
  }

  function test_a_profile_can_add_a_location_data() {
  	$this->profile->addlocationData([
  		'identifier' => 'acme',
      'latitude' => 34.78172000,
      'longitude' => -78.65666900
  	],
    [
      'name' => 'Raleigh',
      'county' => 'Wake County',
      'state' => 'NC' 
    ],
    [
      'legal_biz_name' => 'acme inc',
      'biz_street_address' => '1 Fake Ave',
      'biz_city' => 'Raleigh',
      'biz_state' => 'nc',
      'biz_zip' => '27603',
      'phone' => '910-853-2465'
    ]);
  	$this->assertDatabaseHas('geo_locations', [
      'profile_id' => $this->profile->id,
      'identifier' => 'acme',
    	'latitude' => 34.78172000,
    	'longitude' => -78.65666900
    ]);
    $this->assertDatabaseHas('cities', [
      'name' => 'raleigh',
      'county' => 'wake county',
      'state' => 'nc' 
    ]);
    $this->assertDatabaseHas('accounts', [
      'profile_id' => $this->profile->id,
      'legal_biz_name' => 'acme inc',
      'biz_street_address' => '1 Fake Ave',
      'biz_city' => 'Raleigh',
      'biz_state' => 'nc',
      'biz_zip' => '27603',
      'phone' => '910-853-2465'
    ]);
  }

  function test_a_profile_adds_a_city_if_none_exists() {
    $this->profile->associateCity([
        'name' => 'Raleigh',
        'county' => 'Wake County',
        'state' => 'NC'
    ]);
    $this->assertDatabaseHas("cities", ['name' => 'raleigh']);
    $this->assertEquals('raleigh', $this->profile->city->name);
  }

  function test_a_profile_creates_a_relationship_if_a_city_exists() {
    $city = create('App\City');
    $this->profile->associateCity([
        'name' => 'Raleigh',
        'county' => 'Wake County',
        'state' => 'NC'
    ]);
    $this->assertEquals('raleigh', $this->profile->city->name);
    $this->assertCount(1, City::get());
  }

  function test_a_profile_can_create_a_slug() {
    $profile = create('App\Profile');

    $this->assertEquals($profile->slug, str_slug($profile->business_name, '-'));
  }

  function test_a_profile_slug_is_unique() {
    $profileFirst = create('App\Profile');
    $profileSecond = create('App\Profile', ['business_name' => $profileFirst->business_name]);
    $this->assertNotEquals($profileFirst->slug, $profileSecond->slug);
  }

  function test_a_profile_belongs_to_city() {
    $city = create('App\City');
    $profile = create('App\Profile', ['city_id' => $city->id]);

    $this->assertInstanceOf('App\City', $profile->city);
  }

  function test_a_profile_has_one_account() {
    $profile = create('App\Profile');
    create('App\Account', ['profile_id' => $profile->id]);

    $this->assertInstanceOf('App\Account', $profile->account);
  }
}




