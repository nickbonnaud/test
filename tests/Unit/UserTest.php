<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
  use DatabaseMigrations;


  public function setUp() {
  	parent::setUp();
  }

  function test_a_user_can_set_its_role() {
  	$profile = create('App\Profile');
  	$this->user = create('App\User');
  	$this->user->setRole($profile);

  	$this->assertEquals($profile->id, $this->user->employer_id);
  }
}




