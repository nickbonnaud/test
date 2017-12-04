<?php

namespace Tests\Unit;

use App\Account;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountTest extends TestCase
{
	use RefreshDatabase;

  public function setUp() {
  	parent::setUp();
  }

  function test_an_account_belongs_to_a_profile()
  {
    $account = create('App\Account');
    $this->assertInstanceOf('App\Profile', $account->profile);
  }

  function test_an_account_encrypts_sensitive_data_on_save() {
  	$ssn = '123-45-6789';
  	$accountNumber = '1122334455';
  	$routing = '987654321';

  	$account = make('App\Account', ['ssn' => $ssn, 'account_number' => $accountNumber, 'routing' => $routing]);

  	$account->save();

  	$this->assertNotEquals($account->ssn, $ssn);
  	$this->assertNotEquals($account->account_number, $accountNumber);
  	$this->assertNotEquals($account->routing, $routing);
  }

  function test_an_account_decrypts_and_shortens_sensitive_data() {
  	$ssn = '123-45-6789';
  	$accountNumber = '1122334455';
  	$routing = '987654321';

  	create('App\Account', ['ssn' => $ssn, 'account_number' => $accountNumber, 'routing' => $routing]);

  	$account = Account::first();

  	$this->assertEquals($account->ssn, substr($ssn, -4));
  	$this->assertEquals($account->account_number, substr($accountNumber, -4));
  	$this->assertEquals($account->routing, substr($routing, -4));
  }

  function test_an_account_cleans_annual_credit_card_sales_to_int() {
  	$annual_cc_sales = "$10,002.40";
  	$account = make('App\Account', ['annual_cc_sales' => $annual_cc_sales]);
  	$account->save();

  	$this->assertEquals($account->annual_cc_sales, 10002);
  }

  function test_an_account_stores_ownership_in_basis_points() {
  	$ownership = 50;
  	$account = make('App\Account', ['ownership' => $ownership]);
  	$account->save();

  	$this->assertDatabaseHas('accounts', ['ownership' => $ownership * 100]);
  }

  function test_an_account_returns_ownership_not_in_basis_point() {
  	$ownership = 65;
  	create('App\Account', ['ownership' => $ownership]);

  	$account = Account::first();

  	$this->assertEquals($account->ownership, $ownership);
  }

  function test_an_account_can_create_a_slug() {
    $account = create('App\Account');

    $this->assertEquals($account->slug, str_slug($account->legal_biz_name, '-'));
  }

  function test_an_account_slug_is_unique() {
    $accountFirst = create('App\Account');
    $accountSecond = make('App\Account', ['legal_biz_name' => $accountFirst->legal_biz_name]);

    $this->assertNotEquals($accountFirst->slug, $accountSecond->slug);
  }

  function test_an_account_knows_its_form_stage() {
    $account = new Account([
      'legal_biz_name' => "Acme",
      'business_type' => 0,
      'biz_tax_id' => 12-3456789,
      'established' => '1996-11-13',
      'annual_cc_Sales' => 10000,
      'biz_street_address' => '5877 Watson Land Suite 029',
      'biz_city' => 'Williamsonbury',
      'biz_state' => 'SC',
      'biz_zip' => '95075',
      'phone' => '910-821-1122',
      'account_email' => 'avery.kunze@example.net',
    ]);

    $this->assertEquals('owner', $account->getAccountFormStage());
  }

  function test_an_account_returns_correct_route_based_on_account_completion() {
    $account = create('App\Account', ['owner_email' => null]);
    $this->assertEquals("/accounts/{$account->slug}/edit", $account->route());

    $account1 = create('App\Account', ['method' => null]);
    $this->assertEquals("/accounts/{$account1->slug}/edit", $account1->route());

    $account3 = create('App\Account');
    $this->assertEquals("/accounts/{$account3->slug}", $account3->route());
  }

  function test_an_account_returns_its_payment_method_name() {
    $account = create('App\Account', ['method' => 10]);
    $this->assertEquals("Corporate Checking Account", $account->methodName());
  }

  function test_an_account_returns_its_business_type_name() {
    $account = create('App\Account', ['business_type' => 2]);
    $this->assertEquals("LLC", $account->businessTypeName());
  }
}
