<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayWebHooksController extends Controller {

	public function clover(Request $request) {
		if ($request->header('X-Clover-Auth') == env('CLOVER_WEBHOOK_HEADER')) {
			\Log::debug(json_decode($request->all()));
			return response()->json(['success' => 'authorized'], 200);
		} else {
			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}
}
