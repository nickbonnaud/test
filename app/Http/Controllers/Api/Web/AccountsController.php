<?php

namespace App\Http\Controllers\Api\Web;

use App\Account;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountsController extends Controller {

	public function update(Request $request) {\
		Log::info("hit the endpoint real");
		log::info($request->getContent());
		$business = $request->getContent();
    $account = Account::where('splashId', '=', $business->id)->first();
    if ($business->status == 2) {
      $account->status = "active";
    } elseif ($business->status == 4) {
      $account->status = 'denied';
    } else {
      $account->status = 'pending';
    }
    $account->save();
	}
}
