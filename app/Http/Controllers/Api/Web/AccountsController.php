<?php

namespace App\Http\Controllers\Api\Web;

use App\Account;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountsController extends Controller {

	public function update(Request $request) {
		Log::info("Testing");
		$business = json_decode($request->getContent(), true);
		Log::info(array_get($business, 'response.alert.merchantId'));
		Log::info(array_get($business, 'response.alert.merchantStatus'));

		$merchantId = array_get($business, 'response.alert.merchantId');
		$status = array_get($business, 'response.alert.merchantStatus');

    $account = Account::where('splash_id', '=', $merchantId)->first();
    $account->status = strtolower($status);
    $account->save();
	}
}
