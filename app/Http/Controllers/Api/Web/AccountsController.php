<?php

namespace App\Http\Controllers\Api\Web;

use App\Account;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountsController extends Controller {

	public function update(Request $request) {
		Log::info(json_decode($request->getContent()));
		$business = json_decode($request->getContent());
    $account = Account::where('splashId', '=', $business->merchantId)->first();
    $account->status = $business->status;
    $account->save();
	}
}
