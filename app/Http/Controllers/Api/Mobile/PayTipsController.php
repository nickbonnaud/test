<?php

namespace App\Http\Controllers\Api\Mobile;

use JWTAuth;
use App\Transaction;
use App\Http\Resources\PayTipsResource;
use Illuminate\Support\Facades\Input;
use App\Filters\TransactionFilters;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayTipsController extends Controller {

	public function __construct() {
		$this->middleware('jwt.auth');
	}

	public function index(Request $request, TransactionFilters $filters) {
		$user = JWTAuth::parseToken()->authenticate();
		$profile = $user->profile;

		\Log::debug($request->fullUrl());

		$transactions = Transaction::filter($filters, $profile)->paginate(20)->appends(Input::except('page'));
		return PayTipsResource::collection($transactions);
	}
}
