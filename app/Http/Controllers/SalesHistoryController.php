<?php

namespace App\Http\Controllers;

use App\Profile;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use App\Filters\TransactionFilters;
use App\Filters\UserFilters;

class SalesHistoryController extends Controller {
	
	public function __construct() {
    $this->middleware('auth');
  }

  public function show(Profile $profile, TransactionFilters $transactionFilters, UserFilters $userFilters) {
  	$this->authorize('view', $profile);
  	$sales = Transaction::filter($transactionFilters, $profile)->get();
  	$employees = User::filter($userFilters, $profile)->get();
  	return view('sales_history.show', compact('sales', 'employees'));
  }
}
