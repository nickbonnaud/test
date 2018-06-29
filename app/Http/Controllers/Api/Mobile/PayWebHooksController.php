<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayWebHooksController extends Controller {

	public function clover(Request $request) {
		\Log::debug("Hello World");
		return response()->json(['success' => 'CONNECTED'], 200);
	}
}
