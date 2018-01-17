<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use App\User;
use App\Http\Controllers\Controller;
use SplashPayments;

class CardVaultController extends Controller
{
    
  public function __construct() {
		$this->middleware('jwt.auth');
	}

  public function show() {
   	$user = JWTAuth::parseToken()->authenticate();
   	$user['token'] = $token = JWTAuth::getToken();
    return view('card_vault.show', compact('user'));
  }

  public function store(User $user, Request $request) {
  	$jwtUser = JWTAuth::parseToken()->authenticate();
 		if ($user->id == $jwtUser->id) {
 			$user->last_four_card = $request->numberLastFour;
 			$user->customer_id = $request->token;
 			$success = $user->save();
 			return response()->json($success);
 		} else {
 			return response()->json(['error' => 'invalid_credentials'], 401);
 		}
  }



  private function setCardType($cardType) {
  	switch ($cardType) {
      case 1:
       return 'AMERICAN_EXPRESS';
      case 2:
        return 'VISA';
      case 3:
        return 'MASTERCARD';
      case 4:
        return 'DINERS_CLUB';
      case 5:
        return 'DISCOVER';
      default:
        return 'GENERIC';
    }
  }   
}
