<?php

namespace App;

use Socialite;
use Illuminate\Database\Eloquent\Model;

class InstagramAccount extends Model
{

	public static function getData() {
		$userData = Socialite::driver('instagram')->user();
    self::setAccountDetails($userData);
	}

	public static function setAccountDetails($userData) {
		$profile = auth()->user()->profile;
		$profile->insta_account_id = $userData->id;
		$profile->insta_account_token = $userData->token;
		$profile->connected = 'instagram';
		$profile->save();
		return 'success';
	}

	public static function unSubscribe($profile) {
		$profile->insta_account_token = null;
		$profile->connected = null;
		$profile->save();
	}
}
