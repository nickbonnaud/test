<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\Transaction;
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

		$transactions = Transaction::filter($filters, $profile)->get();
		return ApiTransactionResource::collection($transactions);
	}

	public function store(Request $request) {

	}
}
