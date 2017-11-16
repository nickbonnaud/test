<?php

namespace App\Http\Controllers\Api\Web;

use App\Profile;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use App\Filters\TransactionFilters;
use App\Filters\UserFilters;
use App\Http\Controllers\Controller;

class SalesHistoryController extends Controller
{
  public function __construct() {
    $this->middleware('auth');
  }

  public function getCustomDateRangeSales(Profile $profile, TransactionFilters $transactionFilters, UserFilters $userFilters) {
  	$this->authorize('view', $profile);
  	$sales = Transaction::filter($transactionFilters, $profile)->get();
  	$employees = User::filter($userFilters, $profile)->get();
  	return response()->json(array('sales' => $sales, 'employees' => $employees));
  }
}
