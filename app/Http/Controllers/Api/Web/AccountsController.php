<?php

namespace App\Http\Controllers\Api\Web;

use App\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountsController extends Controller {

	public function update(Request $request) {
		$business = json_decode($request->getContent());
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
