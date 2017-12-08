<?php

namespace App;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
	protected $fillable = [
		'name',
		'county',
		'state'
	];

	public function getRouteKeyName() {
		return 'slug';
	}

	public function setNameAttribute($name) {
		$name = strtolower($name);
		$slug = str_slug($name, '-');
		$count = City::where('name', $name)->count();
		$this->attributes['slug'] = $count > 0 ? "{$slug}-{$count}" : $slug;
		$this->attributes['name'] = $name;
	}

	public function setCountyAttribute($county) {
		$county = strtolower($county);
		$this->attributes['county'] = $county;
	}

	public function setStateAttribute($state) {
		$state = strtolower($state);
		$this->attributes['state'] = $state;
	}

	public function profiles() {
		return $this->hasMany('App\Profile');
	}

	public static function getCurrentLocation($lat, $lng) {
		$location = self::getReverseGeoCode($lat, $lng);
		return $city = self::getCity($location);
	}

	public static function getReverseGeoCode($lat, $lng) {
		$client = new Client(['base_uri' => 'https://maps.googleapis.com/maps/api/geocode/']);
		try {
			$response = $client->request('GET', 'json', [
				'query' => [
					'latlng' => $lat . ',' .  $lng,
					'result_type' => 'locality',
					'key'=> env('GOOGLE_KEY')
				]
			]);
		} catch (GuzzleException $e) {
			if ($e->hasResponse()) {
				dd($e->getResponse());
			}
		}
		return json_decode($response->getBody());
	}

	public static function getCity($location) {
		foreach ($location->results[0]->address_components as $addressComponent) {
			${$addressComponent->types[0]} = strtolower($addressComponent->short_name);
		}
		return $city = City::where('name', '=', $locality)
      ->where('county', '=', $administrative_area_level_2)
      ->where('state', '=', $administrative_area_level_1)->first();
	}
}
