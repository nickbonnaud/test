<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Account;
use Crypt;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_create_an_account() {
  	$this->withExceptionHandling();
  	$profile = create('App\Profile');
  	$account = make('App\Account', ['profile_id' => $profile->id]);

  	$this->post("/accounts/" . $profile->slug, $account->toArray())->assertRedirect('/login');
  	$this->signIn();
    $this->post("/accounts/" . $profile->slug, $account->toArray())->assertStatus(403);
  }

  function test_an_unauthorized_user_cannot_view_create_account_form() {
  	$this->withExceptionHandling();
  	$profile = create('App\Profile');

  	$this->get("/accounts/" . $profile->slug . "/create")->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/accounts/" . $profile->slug . "/create")->assertStatus(403);
  }

  function test_an_authorized_user_can_view_create_account_form() {
    $this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $account = create('App\Account', ['profile_id' => $profile->id, 'legal_biz_name' => $profile->business_name, 'account_email' => null]);


    $this->get("/accounts/" . $profile->slug . "/edit")->assertSee('Business Info');
  }

  function test_an_unathenticated_user_cannot_update_an_account() {
  	$this->withExceptionHandling();
  	$profile = create('App\Profile');
  	$account = create('App\Account', ['profile_id' => $profile->id]);

  	$account['type'] = 'owner';
    $account->owner_email = "fake@email.com";

  	$this->patch("/accounts/" . $account->slug, $account->toArray())->assertRedirect('/login');
  	$this->signIn();
  	$this->patch("/accounts/" . $account->slug, $account->toArray())->assertStatus(403);
  }

  function test_an_authenticated_user_can_update_their_account_owner() {
  	$this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);

    $account['type'] = 'owner';
    $account->owner_email = "fake@email.com";

    $response = $this->patch("/accounts/" . $account->slug, $account->toArray());
    $this->assertDatabaseHas('accounts', ['profile_id' => $profile->id, 'owner_email' => $account->owner_email]);
  }

  function test_an_authenticated_user_can_update_their_account_business() {
  	$this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);

    $account['type'] = 'business';
    $account->account_email = "fakeAccount@email.com";

    $response = $this->patch("/accounts/" . $account->slug, $account->toArray());
    $this->assertDatabaseHas('accounts', ['profile_id' => $profile->id, 'account_email' => $account->account_email]);
  }

  function test_an_authenticated_user_can_update_their_account_pay() {
  	$this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);

    $account['type'] = 'bank';
    $account->method = 2;

    $response = $this->patch("/accounts/" . $account->slug, $account->toArray());
    $this->assertDatabaseHas('accounts', ['profile_id' => $profile->id, 'method' => $account->method]);
  }

  function test_account_updates_do_not_save_masked_ssn() {
  	$this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);

    $data = [
    	'type' => 'owner',
    	'account_user_first' => 'Test',
      'account_user_last' => 'User',
      'date_of_birth' => '1970-12-11',
      'ownership' => 75,
      'indiv_street_address' => '910 Fake St',
      'indiv_city' => 'Raleigh',
      'indiv_state' => 'NC',
      'indiv_zip' => '27603',
      'owner_email' => 'fake@email.com',
      'ssn' => 'XXX-XX-1233',
    ];

    $this->patch("/accounts/" . $account->slug, $data);
    $updatedAccount = Account::first();
    $this->assertEquals($account->ssn, $updatedAccount->ssn);
  }

  function test_account_updates_do_not_save_masked_account_number_routing() {
  	$this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $account = create('App\Account', ['profile_id' => $profile->id]);

    $data = [
    	'type' => 'bank',
    	'routing' => 'XXXXX3456',
      'account_number' => 'XXXXXX1234',
      'method' => 2
    ];

    $this->patch("/accounts/" . $account->slug, $data);
    $updatedAccount = Account::first();
    $this->assertEquals($account->routing, $updatedAccount->routing);
    $this->assertEquals($account->account_number, $updatedAccount->account_number);
  }

  function test_unauthorized_users_cannot_view_account_update_form_pages() {
  	$this->withExceptionHandling();
  	$profile = create('App\Profile');
  	$account = create('App\Account', ['profile_id' => $profile->id]);


  	$this->get("/accounts/" . $account->slug . '/edit')->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/accounts/" . $account->slug . '/edit')->assertStatus(403);
  }

  function test_authorized_user_can_view_account_owner_update_form_after_account_business_update() {
  	$this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $account = create('App\Account', ['profile_id' => $profile->id, 'legal_biz_name' => $profile->business_name, 'biz_street_address' => '5877 Watson Land Suite 029', 'biz_city' => 'Williamsonbury', 'biz_state' => 'SC', 'biz_zip' => '95075', 'phone' => '910-821-1122', 'owner_email' => null]);
 		$data = [
      'type' => 'business',
	    'legal_biz_name' => $profile->business_name,
      'business_type' => 0,
      'biz_tax_id' => 12-3456789,
      'established' => '1996-11-13',
      'annual_cc_sales' => 10000,
      'biz_street_address' => '5877 Watson Land Suite 029',
      'biz_city' => 'Williamsonbury',
      'biz_state' => 'SC',
      'biz_zip' => '95075',
      'phone' => '910-821-1122',
      'account_email' => 'avery.kunze@example.net',
 		];

    
    $response = $this->patch("/accounts/" . $account->slug, $data);
    $response->assertRedirect('/accounts/' . $profile->account->slug . '/edit');

    $this->get($response->headers->get('Location'))
      ->assertSee('Business Owner Info');
  }

  function test_authorized_user_can_view_account_bank_update_form_after_account_owner_update() {
  	$this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $account = create('App\Account', ['profile_id' => $profile->id, 'routing' => null, 'account_number' => null, 'method' => null]);

    $data = [
      'type' => 'owner',
      'account_user_first' => $account->account_user_first,
      'account_user_last' => $account->account_user_last,
      'date_of_birth' => $account->date_of_birth,
      'ownership' => $account->ownership,
      'indiv_street_address' => $account->indiv_street_address,
      'indiv_city' => $account->indiv_city,
      'indiv_state' => $account->indiv_state,
      'indiv_zip' => $account->indiv_zip,
      'owner_email' => $account->owner_email,
      'ssn' => $account->ssn,
    ];
    $response = $this->patch("/accounts/" . $account->slug, $data);
    $response->assertRedirect('/accounts/' . $account->slug . '/edit');

    $this->get($response->headers->get('Location'))
      ->assertSee('Banking Info');
  }

  function test_authorized_user_can_view_account_show_after_account_bank_update() {
  	$this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
    $account = create('App\Account', ['profile_id' => $profile->id, 'routing' => null, 'account_number' => null, 'method' => null]);

    $data = [
    	'routing' => '321456789',
    	'account_number' => '789654321',
    	'method' => 2,
    	'type' => 'bank'
    ];

    $response = $this->patch("/accounts/" . $account->slug, $data);
    $response->assertRedirect('/accounts/' . $account->slug);

    $this->get($response->headers->get('Location'))
      ->assertSee('Your Business Account Profile');
  }

  function test_unauthorized_users_cannot_view_account_show() {
  	$this->withExceptionHandling();
  	$profile = create('App\Profile');
  	$account = create('App\Account', ['profile_id' => $profile->id]);


  	$this->get("/accounts/" . $account->slug)->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/accounts/" . $account->slug)->assertStatus(403);
  }

  function test_authorized_users_can_view_account_show() {
  	$this->signIn();
    $profile = create('App\Profile', ['user_id' => auth()->user()->id]);
  	$account = create('App\Account', ['profile_id' => $profile->id]);

  	$this->get("/accounts/" . $account->slug)->assertSee('Your Business Account Profile');
  }
}
