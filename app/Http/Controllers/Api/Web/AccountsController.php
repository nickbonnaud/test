<?php

namespace App\Http\Controllers\Api\Web;

use App\Account;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountsController extends Controller {

	public function update(Request $request) {
		$business = json_decode($request->getContent(), true);
		Log::info($business);
    $account = Account::where('splashId', '=', $business['merchantId'])->first();
    $account->status = $business->status;
    $account->save();
	}
}
