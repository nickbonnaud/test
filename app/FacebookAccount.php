<?php

namespace App;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Socialite;
use Illuminate\Database\Eloquent\Model;

class FacebookAccount extends Model
{

	public static function getData() {
		$userData = Socialite::driver('facebook')->fields(['accounts'])->user();
    self::getAccountDetails($userData);
	}

	public static function getAccountDetails($userData) {
		$userManagedAccounts = array_get($userData->user, 'accounts.data');

		if (count($userManagedAccounts === 1)) {
			$pageID = array_get($userManagedAccounts, '0.id');
			$accessToken = array_get($userManagedAccounts, '0.access_token');

			self::installApp($pageID, $accessToken);
		} 
	}

	public static function installApp($pageID, $accessToken) {
		$client = new Client(['base_uri' => 'https://graph.facebook.com/v2.8/']);
		try {
			$response = $client->request('POST', $pageID . '/subscribed_apps', [
				'query' => ['access_token' => $accessToken]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		$data = json_decode($response->getBody());
		if ($data->success === true) {
			self::addPageIdToProfile($pageID, $accessToken);
		}
	}

	public static function addPageIdToProfile($pageID, $accessToken) {
		$profile = auth()->user()->profile;
		$profile->fb_page_id = $pageID;
		$profile->fb_app_id = $accessToken;
		$profile->connected = 'facebook';
		$profile->save();
		return 'success';
	}

	public static function subscribe($profile) {
		$accessToken = $profile->fb_app_id;
  	$pageId = $profile->fb_page_id;
  	$client = new Client(['base_uri' => 'https://graph.facebook.com/v2.8/']);
		try {
			$response = $client->request('POST', $pageId . '/subscribed_apps', [
				'query' => ['access_token' => $accessToken]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		$data = json_decode($response->getBody());
		if ($data->success == true) {
			$profile->connected = 'facebook';
			$profile->save();
		}
	}

	public static function unSubscribe($profile) {
		$accessToken = $profile->fb_app_id;
  	$pageId = $profile->fb_page_id;
  	$client = new Client(['base_uri' => 'https://graph.facebook.com/v2.8/']);
		try {
			$response = $client->request('DELETE', $pageId . '/subscribed_apps', [
				'query' => ['access_token' => $accessToken]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		$data = json_decode($response->getBody());
		if ($data->success == true) {
			$profile->connected = null;
			$profile->save();
		}
	}
}
