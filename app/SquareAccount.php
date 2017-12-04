<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class SquareAccount extends Model
{

	public static function getData($code) {
		$client = new Client(['base_uri' => 'https://connect.squareup.com/oauth2/']);
		try {
			$response = $client->request('POST', 'token', [
				'json' => [
					'client_id' => env('SQUARE_ID'),
          'client_secret' => env('SQUARE_SECRET'),
          'code' => $code
				]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		$data = json_decode($response->getBody());
		self::setSquareData($data);
	}

	public static function setSquareData($data) {
		$profile = auth()->user()->profile;
		$profile->square_token = $data->access_token;
		$profile->save();
		return 'success';
	}

	public static function enableLocation($profile) {
		$client = new Client(['base_uri' => 'https://connect.squareup.com/v1/']);
		try {
			$response = $client->request('GET', 'me/locations', [
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
		$locations = json_decode($response->getBody());
		return self::addLocation($locations, $profile);
	}

	public static function addLocation($locations, $profile) {
		$account = $profile->account;
		if (count($locations) > 1) {
			$result = self::matchLocation($locations, $account);
			return $result;
		} else {
			self::saveSquareLocation($account, $locations[0]);
			return 'success_location';
		}
	}

	public static function matchLocation($locations, $account) {
		$businessLocation = $account->biz_street_address;
		foreach ($locations as $location) {
			if ($location->business_address->address_line_1 == $businessLocation) {
				self::saveSquareLocation($account, $location);
				return 'success_location';
			}
		}
		return 'no_match';
	}

	public static function saveSquareLocation($account, $location) {
		$account->square_location_id = $location->id;
		$account->save();
	}

	public static function getInventory($profile) {
		$client = new Client(['base_uri' => 'https://connect.squareup.com/v1/']);
		try {
			$response = $client->request('GET', $profile->account->square_location_id . '/items', [
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
		$inventory = json_decode($response->getBody());
		return $inventory;
	}

	public static function enablePockeytLite($profile) {
		if (! $locationId = $profile->account->square_location_id) {
			$result = self::enableLocation($profile);
			if ($result != 'success_location') {
				return $result;
			}
			$locationId = $profile->account->square_location_id;
		}
		
		$token = $profile->square_token;
		$client = new Client(['base_uri' => 'https://connect.squareup.com/v1/']);

		self::getCategories($locationId, $token, $client, $profile);
		self::getItems($locationId, $token, $client, $profile);
		self::getPages($locationId, $token, $client, $profile);
		self::subscribe($locationId, $token, $client, $profile);
		return 'success_lite';
	}

	public static function getCategories($locationId, $token, $client, $profile) {
		try {
			$response = $client->request('GET', $locationId . '/categories', [
				'headers' => [
					'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
				]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		$categories = json_decode($response->getBody());
		return self::checkCategories($categories, $profile, $locationId, $token, $client);
	}

	public static function getItems($locationId, $token, $client, $profile) {
		try {
			$response = $client->request('GET', $locationId . '/items', [
				'headers' => [
					'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
				]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		$items = json_decode($response->getBody());
		return self::checkItems($items, $profile, $locationId, $token, $client);
	}

	public static function getPages($locationId, $token, $client, $profile) {
		try {
			$response = $client->request('GET', $locationId . '/pages', [
				'headers' => [
					'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
				]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		$pages = json_decode($response->getBody());
		return self::checkPages($pages, $profile, $locationId, $token, $client);
	}

	public static function checkCategories($categories, $profile, $locationId, $token, $client) {
		$storedCategoryId = $profile->account->square_category_id;
		foreach ($categories as $category) {
			if ($category->name == "Pockeyt Customers" || (isset($storedCategoryId) && ($storedCategoryId == $category->id))) {
				$account = $profile->account;
				$account->square_category_id = $category->id;
				return $account->save();
			}
		}
		return self::createCategory($locationId, $token, $client, $profile);
	}

	public static function checkItems($items, $profile, $locationId, $token, $client) {
		$storedItemId = $profile->account->square_item_id;
		foreach ($items as $item) {
			if ($item->name == "Pockeyt Customers" || (isset($storedItemId) && ($storedItemId == $item->id))) {
				$account = $profile->account;
				$account->square_item_id = $item->id;
				return $account->save();
			}
		}
		return self::createItem($locationId, $token, $client, $profile);
	}

	public static function checkPages($pages, $profile, $locationId, $token, $client) {
		if (count($pages) > 0) {
			foreach ($pages as $page) {
				return self::createCell(4, 4, $page->id, $token, $locationId, $client, $profile);
			}
		}
		return self::createPage($profile, $locationId, $token, $client);
	}

	public static function createCategory($locationId, $token, $client, $profile) {
		try {
			$response = $client->request('POST', $locationId . '/categories', [
				'headers' => [
					'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
				],
				'json' => ['name' => 'Pockeyt Customers']
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		$category = json_decode($response->getBody());
		$account = $profile->account;
		$account->square_category_id = $category->id;
		return $account->save();
	}

	public static function createItem($locationId, $token, $client, $profile) {
		try {
			$response = $client->request('POST', $locationId . '/items', [
				'headers' => [
					'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
				],
				'json' => [
					'name' => 'Pockeyt Customer',
          'category_id' => $profile->account->square_category_id,
          'abbreviation' => 'PC',
          'variations' => [
          	[
          		'name' => 'Placeholder default Pockeyt Customer',
              'pricing_type' => 'FIXED_PRICING',
              'price_money' => [
                'currency_code' => 'USD',
                'amount' => 0,
              ]
          	]
          ]
				]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		$item = json_decode($response->getBody());
		$account = $profile->account;
		$account->square_item_id = $item->id;
		return $account->save();
	}

	public static function createCell($row, $column, $pageId, $token, $locationId, $client, $profile) {
		try {
			$response = $client->request('PUT', $locationId . '/pages' . '/' . $pageId . '/cells', [
				'headers' => [
					'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
				],
				'json' => [
					'row' => $row,
          'column' => $column,
          'object_type' => 'ITEM',
          'object_id' =>  $profile->account->square_item_id
				]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		return;
	}

	public static function createPage($profile, $locationId, $token, $client) {
		try {
			$response = $client->request('POST', $locationId . '/pages', [
				'headers' => [
					'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
				],
				'json' => [
					'page_index' => 0
				]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		$page = json_decode($response->getBody());
		return self::createCell(4, 4, $page->id, $token, $locationId, $client, $profile);
	}

	public static function subscribe($locationId, $token, $client, $profile) {
		try {
			$response = $client->request('PUT', $locationId . '/webhooks', [
				'headers' => [
					'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json'
				],
				'json' => [
					'PAYMENT_UPDATED'
				]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		$account = $profile->account;
    $account->pockeyt_lite_enabled = true;
    return $account->save();
	}

	public static function disablePockeytLite($profile) {
		$client = new Client(['base_uri' => 'https://connect.squareup.com/v1/']);
		try {
			$response = $client->request('PUT', $profile->account->square_location_id . '/webhooks', [
				'headers' => [
					'Authorization' => 'Bearer ' . $profile->square_token,
          'Accept' => 'application/json'
				],
				'json' => []
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		$account = $profile->account;
    $account->pockeyt_lite_enabled = false;
    $account->save();
    return 'pockeyt_lite_disabled';
	}
}
