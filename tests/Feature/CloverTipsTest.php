<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CloverTipsTest extends TestCase
{
  use RefreshDatabase;

  function test_an_unauthorized_clover_client_cannot_retrieve_a_tips_data() {
    $this->withExceptionHandling();
    $profile = create('App\Profile');
    $user = create('App\User');

    $employeeId = '1234qwer';

    $transaction = create('App\Transaction', ['profile_id' => $profile->id, 'user_id' => $user->id, 'employee_id' => $employeeId]);

    $response = $this->get("/api/mobile/pay/tips?employeeTips[]={$employeeId}")->assertStatus(401);
  }

  function test_an_authorized_clover_client_can_retrieve_a_single_employees_tips() {
    $user = create('App\User');
    $profile = create('App\Profile', ['user_id' => $user->id]);

    $employeeId = '1234qwer';
    $transactionId = "vmdih37";

    $transactionOfEmployee = create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $employeeId, 'status' => 20, 'refund_full' => false, 'pos_transaction_id' => $transactionId]);

    $employeeId1 = 'vfjkd7s';
    $transactionId1 = "bjcsbjvd";

    $transactionNotEmployee = create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $employeeId1, 'status' => 20, 'refund_full' => false, 'pos_transaction_id' => $transactionId1]);

    $response = $this->get("/api/mobile/pay/tips?employeeTips[]={$employeeId}", $this->headers($user))->getData();

    $this->assertEquals($employeeId, $response->data[0]->employee_id);
    $this->assertEquals($transactionId, $response->data[0]->transaction_id);
    $this->assertCount(1, $response->data);
  }

  function test_an_authorized_clover_client_can_retrieve_a_all_employees_tips() {
    $user = create('App\User');
    $profile = create('App\Profile', ['user_id' => $user->id]);

    $employeeId = '1234qwer';
    $transactionId = "vmdih37";

    $transactionOfEmployee = create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $employeeId, 'status' => 20, 'refund_full' => false, 'pos_transaction_id' => $transactionId]);

    $employeeId1 = 'vfjkd7s';
    $transactionId1 = "bjcsbjvd";

    $transactionOfEmployee1 = create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $employeeId1, 'status' => 20, 'refund_full' => false, 'pos_transaction_id' => $transactionId1]);

    $response = $this->get("/api/mobile/pay/tips?allTips=1", $this->headers($user))->getData();

    $this->assertEquals($employeeId1, $response->data[1]->employee_id);
    $this->assertEquals($transactionId1, $response->data[1]->transaction_id);
    $this->assertCount(2, $response->data);
  }

  function test_an_authorized_user_can_get_tip_details_for_custom_time_range() {
  	$user = create('App\User');
    $profile = create('App\Profile', ['user_id' => $user->id]);

    $employeeId = '1234qwer';

    $transactionId1 = "vmdih37";
    create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $employeeId, 'status' => 20, 'refund_full' => false, 'pos_transaction_id' => $transactionId1, 'created_at' => (Carbon::now())->subDay()]);

    $transactionId3 = "vmdih35";
    create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $employeeId, 'status' => 20, 'refund_full' => false, 'pos_transaction_id' => $transactionId3, 'created_at' => (Carbon::now())->subDays(2)]);

    $transactionId4 = "vmdih34";
    create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $employeeId, 'status' => 20, 'refund_full' => false, 'pos_transaction_id' => $transactionId4, 'created_at' => (Carbon::now())->subhours(2)]);

     $transactionId2 = "vmdih36";
    create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $employeeId, 'status' => 20, 'refund_full' => false, 'pos_transaction_id' => $transactionId2, 'created_at' => ((Carbon::now())->subDay())->subHour()]);

    $startDate = ((Carbon::now())->subDay())->subHours(2)->toDateString();
    $startTime = ((Carbon::now())->subDay())->subHours(2)->toTimeString();
    $startDateTime = $startDate . '_' . $startTime;


    $endDate= ((Carbon::now())->subDay())->addHours(6)->toDateString();
    $endTime = ((Carbon::now())->subDay())->addHours(6)->toTimeString();
    $endDateTime = $endDate . '_' . $endTime;


    $response = $this->get("/api/mobile/pay/tips?allTips=1&startTime=" . $startDateTime . '&endTime=' . $endDateTime, $this->headers($user))->getData();

    $this->assertEquals(2, $response->meta->total);
    $this->assertEquals($transactionId1, $response->data[0]->transaction_id);
    $this->assertEquals($transactionId2, $response->data[1]->transaction_id);
  }

  function test_an_authorized_user_can_get_tips_for_specific_employees() {
    $user = create('App\User');
    $profile = create('App\Profile', ['user_id' => $user->id]);

    $employeeId = '1234qwer';
    $transactionId = "vmdih37";

    $transactionOfEmployee = create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $employeeId, 'status' => 20, 'refund_full' => false, 'pos_transaction_id' => $transactionId]);

    $employeeId1 = 'vfjkd7s';
    $transactionId1 = "bjcsbjvd";

    $transactionOfEmployee1 = create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $employeeId1, 'status' => 20, 'refund_full' => false, 'pos_transaction_id' => $transactionId1]);

    $employeeId2 = 'frnj444';
    $transactionId2 = 'cbdhsvhdsv';

    $transactionOfEmployee2 = create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $employeeId2, 'status' => 20, 'refund_full' => false, 'pos_transaction_id' => $transactionId2]);


    $response = $this->get("/api/mobile/pay/tips?employeeTips[]={$employeeId}&employeeTips[]={$employeeId1}", $this->headers($user))->getData();

    $this->assertEquals($employeeId1, $response->data[1]->employee_id);
    $this->assertEquals($transactionId1, $response->data[1]->transaction_id);
    $this->assertCount(2, $response->data);
  }
}
