<?php

namespace App;

use Carbon\Carbon;
use App\UserLocation;
use Illuminate\Database\Eloquent\Model;

class GeoLocation extends Model {
  /**
   * Fillable fields for a Product
   *
   * @var array
   */
  protected $fillable = [
  	'identifier',
  	'profile_id',
  	'latitude',
  	'longitude'
  ];

  public static function boot() {
    parent::boot();
  }

  /**
   * A Profile belongs to its profile
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function profile() {
    return $this->belongsTo(Profile::class);
  }

  public function updateTaxRate($county, $state) {
    $profile = $this->profile->addTaxRate($county, $state);
    $profile->save();
  }

  public function path() {
    return "/geoLocation/{$this->id}";
  }

  public function scopeFilter($query, $filters) {
    return $filters->apply($query);
  }

  public static function getLocationsInRadius($coords, $geoLocations) {
    $locations = $geoLocations->filter(function($geoLocation) use ($coords) {
      $distance = self::getDistance($coords, $geoLocation);
      return $distance <= 1000;
    });
    return $locations;
  }


  public static function getDistance($coords, $geoLocation) {
    $r = 6371000;
    $dLat = self::deg2rad(($coords['latitude']) - ($geoLocation['latitude']));
    $dLon = self::deg2rad(($coords['longitude']) - ($geoLocation['longitude'])); 
    $a = 
      sin($dLat/2) * sin($dLat/2) +
      cos(deg2rad($geoLocation['latitude'])) * cos(deg2rad($coords['latitude'])) * 
      sin($dLon/2) * sin($dLon/2)
      ; 
    $c = 2 * atan2(sqrt($a), sqrt(1-$a)); 
    $d = $r * $c;
    return $d;
  }

  private static function deg2rad($deg) {
    return $deg * (M_PI/180);
  }
}
