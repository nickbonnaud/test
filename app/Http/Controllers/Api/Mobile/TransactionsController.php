<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\Profile;
use App\Transaction;
use app\UserLocation;
use App\Filters\TransactionFilters;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Http\Resources\ApiTransactionResource;
use App\Http\Controllers\Controller;

class TransactionsController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function index(Request $request, TransactionFilters $filters) {
		$user = JWTAuth::parseToken()->authenticate();
		if ($request->has('dealsAll')) {
			$transactions = Transaction::apiFilter($filters, $user)->get();
			return response()->json(['data' => $transactions]);
		} else {
			if ($request->has('unRedeemedDeals')) {
				$transactions = Transaction::apiFilter($filters, $user)->get();
			} else {
				$transactions = Transaction::apiFilter($filters, $user)->paginate(10)->appends(Input::except('page'));
			}
			return ApiTransactionResource::collection($transactions);
		}
	}

	public function update(Profile $profile, Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$transaction = Transaction::with('profile')->findOrFail($request->id);
		if (!($transaction->user_id == $user->id)) return response()->json(['error' => 'Unauthorized'], 401);
		$transaction->update($request->all());
		$transaction->transactionChangeEvent();
		if ($request->status == 2 || $request->status == 3 || $request->status == 4) {
			$transaction->transactionErrorEvent();
			$success = true;
			$type = 'user_decline';
		} elseif ($request->status == 12) {
			$transaction->customerRequestBillEvent();
			$success = true;
			$type = 'request_bill';
		} elseif ($request->status == 10) {
			$userLocation = UserLocation::where('user_id', $user->id)->where('profile_id', $profile->id)->first();
			$userLocation->touch();


			if ($user->pushToken->device === 'android') {
				$notifications = $user->notifications()->where('data->data->custom->transactionId', $transaction->id)->where('type', 'App\Notifications\PayOrKeepOpenNotification')->get();
			} else {
				$notifications = $user->notifications()->where('data->data->transactionId', $transaction->id)->where('type', 'App\Notifications\PayOrKeepOpenNotification')->get();
			}

			foreach ($notifications as $notification) {
				$notification->delete();
			}



			$success = true;
			$type = 'keep_open';
		} else {
			$success = $transaction->processCharge($request->tip);
			$type = 'user_pay';
		}
		return response()->json(['success' => $success, 'type' => $type], 200);
	}

	public function store(Profile $profile, Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		$transaction = $profile->transactions()->create([
			'user_id' => $user->id
		]);
		if ($request->deal_id) {
			$success = $transaction->processDeal($request->deal_id);
			$type = 'user_deal';
			$transaction = $transaction->fresh();
			$dealId = [
				'deal_id' => $transaction->deal_id,
				'redeemed' => $transaction->redeemed,
				'created_at' => $transaction->created_at
			];
			return response()->json(['success' => $success, 'type' => $type, 'dealId' => $dealId], 200);
		}
	}
}
