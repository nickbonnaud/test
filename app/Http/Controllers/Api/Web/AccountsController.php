<?php

namespace App\Http\Controllers\Api\Web;

use App\Account;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountsController extends Controller {

	public function update(Request $request) {
		$business = json_decode($request->getContent(), true);
		$merchantId = array_get($business, 'merchantData.merchantId');
		$status = array_get($business, 'merchantData.status');

    $account = Account::where('splashId', '=', $merchantId)->first();
    $account->status = $status;
    $account->save();
	}
}