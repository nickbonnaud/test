<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
	use RefreshDatabase;

	function test_a_transaction_belongs_to_a_profile() {
    $transaction = create('App\Transaction');
    $this->assertInstanceOf('App\Profile', $transaction->profile);
  }

  function test_a_transaction_belongs_to_a_user() {
    $transaction = create('App\Transaction');
    $this->assertInstanceOf('App\User', $transaction->user);
  }
}
