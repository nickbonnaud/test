<?php

namespace App\Http\Controllers\Api\Web;

use App\Account;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountsController extends Controller {

	public function update(Request $request) {
		Log::info("new work");
		$business = json_decode($request->getContent(), true);

		Log::info(array_get($business, 'merchantData.merchantId'));
   
	}
}
