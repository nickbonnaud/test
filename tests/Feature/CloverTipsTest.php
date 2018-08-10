<?php

namespace Tests\Feature;

use Tests\TestCase;
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

    $response = $this->get("/api/mobile/pay/tips?employeeTips={$employeeId}")->assertStatus(401);
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

    $response = $this->get("/api/mobile/pay/tips?employeeTips={$employeeId}", $this->headers($user))->getData();

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

    $transactionNotEmployee = create('App\Transaction', ['profile_id' => $profile->id, 'employee_id' => $employeeId1, 'status' => 20, 'refund_full' => false, 'pos_transaction_id' => $transactionId1]);

    $response = $this->get("/api/mobile/pay/tips?allTips=1", $this->headers($user))->getData();

    $this->assertEquals($employeeId1, $response->data[1]->employee_id);
    $this->assertEquals($transactionId1, $response->data[1]->transaction_id);
    $this->assertCount(2, $response->data);
  }
}
