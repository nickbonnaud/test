<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BillTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_view_bill_show() {
		$this->withExceptionHandling();
  	$profile = create('App\Profile');
  	$user = create('App\User');

  	$this->get("/bill/{$profile->slug}/{$user->id}?employee=none&bill=get")->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/bill/{$profile->slug}/{$user->id}?employee=none&bill=new")->assertStatus(403);
	}

	function test_an_authorized_user_can_view_bill_show_new_no_employee() {
  	$this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
  	$user = create('App\User');
  	$this->get("/bill/{$profile->slug}/{$user->id}?")->assertSee($user->first_name . ' ' . $user->last_name . ' ' . 'Bill');
	}

	function test_an_authorized_user_can_view_bill_show_new_with_employee() {
  	$this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
  	$user = create('App\User');
  	$employee = create('App\User', ['employer_id' => $profile->id]);
  	$response = $this->get("/bill/{$profile->slug}/{$user->id}?employee={$employee->id}");

    $employeeId = $response->getOriginalContent()->getData()['employeeId'];
    $response->assertSee($user->first_name . ' ' . $user->last_name . ' ' . 'Bill');
    $this->assertEquals($employeeId, $employee->id);
	}

	function test_an_authorized_user_can_view_bill_show_current_bill_no_employee() {
  	$this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
  	$user = create('App\User');
  	$products = json_encode(['name' => 'Coffee', 'price' => 100, 'quantity' => 1]);

  	$transactionOpen = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => false, 'refund_full' => false, 'products' => $products]);
  	$this->get("/bill/{$profile->slug}/{$user->id}?bill={$transactionOpen->id}")->assertSee('Coffee');
	}

	function test_an_authorized_user_can_view_bill_show_current_bill_with_employee() {
  	$this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
  	$user = create('App\User');
  	$employee = create('App\User', ['employer_id' => $profile->id]);
  	$products = json_encode(['name' => 'Coffee', 'price' => 100, 'quantity' => 1]);

  	$transactionOpen = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'paid' => false, 'refund_full' => false, 'products' => $products, 'employee_id' => $employee->id]);
  	$response = $this->get("/bill/{$profile->slug}/{$user->id}?employee={$employee->id}&bill={$transactionOpen->id}");
    $employeeId = $response->getOriginalContent()->getData()['employeeId'];

    $this->assertEquals($employeeId, $employee->id);
  	$response->assertSee('Coffee');
	}

	function test_an_unauthorized_user_cannot_retrieve_products_on_bill_show() {
		$this->withExceptionHandling();
  	$profile = create('App\Profile');
  	$product = create('App\Product', ['profile_id' => $profile->id]);
  	$user = create('App\User');

  	$this->get("/api/web/products/{$profile->slug}")->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/api/web/products/{$profile->slug}")->assertStatus(403);
	}

	function test_an_authorized_user_can_retrieve_products_on_bill_show() {
  	$this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->user()->id]);
  	$product = create('App\Product', ['profile_id' => $profile->id]);
  	$user = create('App\User');

  	$response = $this->get("/api/web/products/{$profile->slug}")->getData();
  	$this->assertCount(1, $response->data);
  	$this->assertEquals($product->id, $response->data[0]->id);
	}
}
