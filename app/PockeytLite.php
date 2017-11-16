<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class PockeytLite extends Model
{

	public static function addUser($profile, $user) {
		$client = new Client(['base_uri' => 'https://connect.squareup.com/v1/']);
		try {
			$response = $client->request('POST', $profile->account->square_location_id . '/items/' . $profile->account->square_item_id . '/variations', [
				'headers' => [
					'Authorization' => 'Bearer ' . $profile->square_token,
          'Accept' => 'application/json'
				],
				'json' => [
					'id' => 'pockeyt' . $user->id,
					'name' => $user->first_name . ' ' . $user->last_name,
					'price_money' => [
            'currency_code' => 'USD',
            'amount' => 0,
          ]
				]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		return;
	}

	public static function removeUser($profile, $user) {
		$client = new Client(['base_uri' => 'https://connect.squareup.com/v1/']);

		try {
			$response = $client->request('DELETE', $profile->account->square_location_id . '/items/' . $profile->account->square_item_id . '/variations/pockeyt' . $user->id, [
				'headers' => [
					'Authorization' => 'Bearer ' . $profile->square_token,
          'Accept' => 'application/json'
				]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		return;
	}
}