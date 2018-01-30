<?php

namespace App\Http\Controllers\Api\Web;

use App\Account;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountsController extends Controller {

	public function update(Request $request) {
		$business = json_decode($request->getContent(), true);
		Log::alert("INIT!!!");
		Log::alert($business);
		$merchantId = array_get($business, 'merchantData.merchantId');
		$status = array_get($business, 'merchantData.status');

    $account = Account::where('splash_id', '=', $merchantId)->first();
    $account->status = $status;
    $account->save();
	}
}
