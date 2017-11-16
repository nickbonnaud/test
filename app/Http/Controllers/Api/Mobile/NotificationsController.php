<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions;
use App\Http\Controllers\Controller;

class NotificationsController extends Controller {

	public function index(Request $request) {
		try {
      if (!$user = JWTAuth::parseToken()->authenticate()) {
      	return response()->json(['error' => 'user_not_found'], 404);
      }
    } catch (Exceptions\TokenExpiredException $e) {
        return response()->json(['error' => 'token_expired']);
    } catch (Exceptions\TokenInvalidException $e) {
        return response()->json(['error' => 'token_invalid']);
    } catch (Exceptions\JWTException $e) {
        return response()->json(['error' => 'token_absent']);
    }

    $openBill = $user->notifications->filter(function($notif) {
    	return (str_replace_first("App\\Notifications\\",'', $notif->type)) == 'TransactionBillWasClosed';
    })->first();

    return response()->json(['open_bill' => $openBill->data['data']['custom']]);
	}
}
