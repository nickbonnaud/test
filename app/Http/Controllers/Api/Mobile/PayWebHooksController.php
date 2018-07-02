<?php

namespace App\Http\Controllers\Api\Mobile;

use App\ConnectedPos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayWebHooksController extends Controller {

	public function clover(Request $request) {
		if ($request->header('X-Clover-Auth') == env('CLOVER_WEBHOOK_HEADER')) {
			\Log::debug('++++++++++++++++++++++++++++++++++++++++++++');
			\Log::debug($request->all());

			// $webHookData = $request->all();
			// $merchants = $webHookData['merchants'];
			// foreach ($merchants as $merchantIdKey => $orderData) {
			// 	$connectedPos = ConnectedPos::where('merchant_id', $merchantIdKey)->first();
			// 	if ($connectedPos) {
			// 		$connectedPos->parseWebHookData($orderData);
			// 	}
			// }
			return response()->json(['success' => 'authorized'], 200);
		} else {
			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}
}