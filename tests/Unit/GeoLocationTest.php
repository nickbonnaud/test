<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GeoLocationTest extends TestCase
{
  use DatabaseMigrations;

  public function setUp() {
  	parent::setUp();
  	$this->geoLocation = create('App\GeoLocation');
  }


  /**
   * A basic test example.
   *
   * @return void
   */
  public function test_a_geoLocation_belongs_to_a_profile()
  {
    $this->assertInstanceOf('App\Profile', $this->geoLocation->profile);
  }

  public function test_a_geoLocation_can_update_profile_tax_rate()
  {
    $tax = create('App\Tax');
    $geoLocation = create('App\GeoLocation');

    $geoLocation->updateTaxRate($tax->county, $tax->state);
    $this->assertEquals($geoLocation->profile->tax->total, $tax->county_tax + $tax->state_tax);
  }
}
