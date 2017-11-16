<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesHistoryTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_view_sales_history() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');

		$this->get("/sales/{$profile->slug}")->assertRedirect('/login');
  	$this->signIn();
  	$this->get("/sales/{$profile->slug}")->assertStatus(403);
	}

	function test_an_authorized_user_can_view_sales_history_default() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id, 'tip_tracking_enabled' => true]);
		$user = create('App\User', ['employer_id' => $profile->id]);
		$sale = create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $user->id]);

		$this->get("/sales/{$profile->slug}?defaultDate=1")
			->assertSee('Sales Center')
			->assertSee($user->first_name)
			->assertSee("{$sale->total}");
	}

	function test_an_authorized_user_can_view_custom_sales_history() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id, 'tip_tracking_enabled' => true]);
		$user = create('App\User', ['employer_id' => $profile->id]);
		$updatedOn = Carbon::now();
		$updatedOn->subHours(2);
		$sale = create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $user->id, 'updated_at' => $updatedOn, 'refund_full' => false]);

		$endDayTime = Carbon::now();
		$startDayTime = Carbon::yesterday();

		$response = $this->get("/api/web/sales/{$profile->slug}?customDate[]=" . $startDayTime . "&customDate[]=" . $endDayTime)->getData();

		$this->assertEquals($response->sales[0]->id, $sale->id);
		$this->assertEquals($response->employees[0]->id, $user->id);

		
		$endDayTime = Carbon::yesterday();
		$startDayTime = Carbon::now();
		$startDayTime->subdays(2);

		$response = $this->get("/api/web/sales/{$profile->slug}?customDate[]=" . $startDayTime . "&customDate[]=" . $endDayTime)->getData();

		$this->assertEquals($response->sales, []);
		$this->assertEquals($response->employees, []);
	}
}
