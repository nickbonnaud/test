<?php

namespace App\Http\Controllers\Api\Web;

use App\Profile;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use App\Filters\TransactionFilters;
use App\Http\Resources\TransactionResource;
use App\Http\Controllers\Controller;

class TransactionsController extends Controller
{
	public function __construct() {
    $this->middleware('auth');
  }

  public function index(Profile $profile, TransactionFilters $transactionFilters) {
  	$this->authorize('view', $profile);
  	$transactions = Transaction::filter($transactionFilters, $profile)->with('user')->get();
  	return TransactionResource::collection($transactions);
  }

 	public function store(Profile $profile, User $user, Request $request) {
    $this->authorize('view', $profile);
    $transaction = new Transaction($request->all());
    $success = $transaction->save();
    if ($success) {
      $transaction->updateCustomerEvent();
      return response()->json(['transaction' => $transaction, 'success' => true]);
    } else {
      return response()->json(['success' => false]);
    }
  }

  public function update(Profile $profile, Transaction $transaction, Request $request) {
		$this->authorize('update', $transaction);
		$success = $transaction->update($request->all());
    if ($success) {
      $transaction->updateCustomerEvent();
    }
		return response()->json(['transaction' => $transaction, 'success' => $success]);
	}
}
