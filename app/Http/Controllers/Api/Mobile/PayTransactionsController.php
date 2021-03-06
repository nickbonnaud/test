<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\Transaction;
use App\User;
use App\UserLocation;
use App\Filters\TransactionFilters;
use App\Http\Resources\ApiTransactionResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayTransactionsController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function index(Request $request, TransactionFilters $filters) {
		$user = JWTAuth::parseToken()->authenticate();
		$profile = $user->profile;

		$transaction = Transaction::filter($filters, $profile)->first();

		if (!$transaction) {
			return response()->json(['error' => 'no_transaction_matching_criteria'], 404);
		}
		return new ApiTransactionResource($transaction);
	}

	public function store(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$profile = $user->profile;

		if ($request->pos_type == 'clover') {
			$transaction = $this->findOrCreateTransaction($request, $profile);
			$transaction->bill_closed = true;
			$success = $transaction->save();
			if ($success) {
				$transaction->updateCustomerEvent();
				$this->setExitNotificationSentIfCustomerExited($request, $profile);
				return response()->json(['success' => 'waiting_customer_approval'], 200);
			} else {
				return response()->json(['error' => 'unable_to_charge_customer'], 500);
			}
		}
	}



	private function setExitNotificationSentIfCustomerExited($request, $profile) {
		$userLocation = UserLocation::where('profile_id', $profile->id)->where('user_id', $request->user_id)->first();
		$userLocation->exit_notification_sent = $userLocation->customer_exited;
		$userLocation->save();
	}

	private function findOrCreateTransaction($request, $profile) {
		if ($request->transaction_id) {
			$transaction = Transaction::where('id', $request->transaction_id)->first();
			if ($transaction->total != $request->total) {
				$connectedPos = $profile->connectedPos;
				$cloverTransaction = $connectedPos->getTransactionData($request->pos_transaction_id);
				$data = $connectedPos->getLineItems($request->pos_transaction_id);

				$total = $cloverTransaction->total;
				$products = $data['products'];
				$subTotalAndTax = $connectedPos->getCloverTransactionSubtotalAndTax($products, $total);

				$transaction->update([
					'products' => json_encode($products),
		      'tax' => $subTotalAndTax['tax'],
	      	'net_sales' => $subTotalAndTax['subTotal'],
	      	'total' => $total,
	      	'employee_id' => $request->employee_id
				]);
			} elseif (!$transaction->employee_id) {
				$transaction->update([
	      	'employee_id' => $request->employee_id
				]);
			}
			return $transaction;
		} else {
			$connectedPos = $profile->connectedPos;
			$data = $connectedPos->getLineItems($request->pos_transaction_id);

			$customer = User::where('id', $request->user_id)->first();
			$products = $data['products'];
			$total = $request->total;
			$subTotalAndTax = $connectedPos->getCloverTransactionSubtotalAndTax($products, $total);

			return new Transaction([
				'profile_id' => $profile->id,
	      'user_id' => $customer->id,
	      'paid' => false,
	      'bill_closed' => false,
	      'status' => 10,
	      'products' => json_encode($products),
	      'tax' => $subTotalAndTax['tax'],
	      'net_sales' => $subTotalAndTax['subTotal'],
	      'total' => $total,
	      'pos_transaction_id' => $request->pos_transaction_id,
	      'employee_id' => $request->employee_id
			]);
		}
	}
}
