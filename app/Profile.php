<?php

namespace App;

use App\Tax;
use App\City;
use App\FacebookAccount;
use App\InstagramAccount;
use App\SquareAccount;
use App\PockeytLite;
use App\Account;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
	 /**
   * Fillable fields for a flyer
   *
   * @var
   */
  protected $fillable = [
    'business_name',
    'website',
    'description',
    'review_url',
    'review_intro',
    'logo_photo_id',
    'hero_photo_id',
    'featured',
    'lat',
    'lng',
    'tip_tracking_enabled',
    'approved',
    'google_id',
    'google_rating'
  ];

  protected $casts = [
    'approved' => 'boolean',
    'featured' => 'boolean',
  ];

  protected $appends = ['formatted_description'];

  public function toDetailedArray() {
    $data = array_only($this->toArray(), ['id', 'business_name', 'website', 'description', 'review_url', 'review_intro', 'formatted_description', 'created_at', 'updated_at', 'posts', 'tags', 'featured', 'lat', 'long']);
    $data['logo_thumbnail'] = is_null($this->logo) ? '' : $this->logo->thumbnail_url;
    $data['logo'] = is_null($this->logo) ? '' : $this->logo->url;
    $data['hero_thumbnail'] = is_null($this->hero) ? '' : $this->hero->thumbnail_url;
    $data['hero'] = is_null($this->hero) ? '' : $this->hero->url;

    return $data;
  }

  public function getRouteKeyName() {
    return 'slug';
  }

  public static function locatedAt($business_name) {
    $business_name = str_replace('-', ' ', $business_name);
    return static::where(compact('business_name'))->firstOrFail();
  }

  public function logo() {
    return $this->belongsTo('App\Photo', 'logo_photo_id');
  }

  public function hero() {
    return $this->belongsTo('App\Photo', 'hero_photo_id');
  }

  public function user() {
    return $this->belongsTo('App\User', 'user_id');
  }

  public function ownedBy(User $user) {
    return $this->user_id == $user->id;
  }

  public function posts() {
    return $this->hasMany('App\Post');
  }

  public function products() {
    return $this->hasMany('App\Product');
  }

  public function transactions() {
    return $this->hasMany('App\Transaction');
  }

  public function owns($relation) {
    return $relation->profile_id == $this->id;
  }

  public function tags() {
  	return $this->belongsToMany('App\Tag')->withTimestamps();
  }

  public function account() {
    return $this->HasOne(Account::class);
  }

  public function geoLocation() {
    return $this->hasOne(GeoLocation::class);
  }

  public function beacon() {
    return $this->hasOne(Beacon::class);
  }

  public function loyaltyProgram() {
    return $this->hasOne(LoyaltyProgram::class);
  }

  public function connectedPos() {
    return $this->hasOne(ConnectedPos::class);
  }

  public function city() {
    return $this->belongsTo(City::class);
  }

  public function tax() {
    return $this->belongsTo(Tax::class);
  }

  public function createAccount(Account $account) {
    $account->status = "review";
    $this->account()->save($account);
    return $account;
  }

  public function getTagListAttribute() {
  	return $this->tags->pluck('id')->all();
  }

  public function getFormattedDescriptionAttribute() {
    return html_newlines_to_p($this->description);
  }

  public function setWebsiteAttribute($url) {
    if (starts_with($url, 'www')) {
      $this->attributes['website'] = "http://" . $url;
    } elseif ((!starts_with($url, 'www')) && (!starts_with($url, 'http://'))) {
      $this->attributes['website'] = "http://www." . $url;
    } else {
      $this->attributes['website'] = $url;
    }
  }

  public function setBusinessNameAttribute($businessName) {
    $slug = str_slug($businessName, '-');
    $count = Profile::where('business_name', '=', $businessName)->count();
    $this->attributes['slug'] = $count > 0 ? "{$slug}-{$count}" : $slug;
    $this->attributes['business_name'] = $businessName;
  }

  public function scopeApproved($query) {
    return $query->where('approved', true);
  }

  public function scopeVisible($query) {
    return $query->where(function($query) {
      $query = $query->approved();
      if(\Auth::check()) {
          $query = $query->orWhere('user_id', \Auth::id());
      }
      return $query;
    });
  }

  public function scopeFeatured($query) {
    return $query->where('featured', true);
  }

  public function scopeNotFeatured($query) {
    return $query->where('featured', false);
  }

  public function addTaxRate($county, $state) {
    $tax = Tax::where(function($query) use ($county, $state) {
      $query->where('county', '=', strtolower($county))
            ->where('state', '=', strtolower($state));
    })->first();
    if ($tax) {
      $this->tax()->associate($tax);
    }
    return $this;
  }

  public function addlocationData($geoLocationData, $cityData, $accountData) {
   $this->geoLocation()->create($geoLocationData);
   $this->associateCity($cityData);
   $this->createAccount(new Account($accountData));
   return $this;
  }

  public function associateCity($cityData) {
    $city = City::where('name', '=', strtolower($cityData['name']))
      ->where('county', '=', strtolower($cityData['county']))
      ->where('state', '=', strtolower($cityData['state']))->first();
    if (!$city) {
      $city = $this->city()->create($cityData);
    }
    $this->city()->associate($city);
    $this->save();
  }

  public function path() {
    return "/profiles/{$this->slug}";
  }

  public function associatePhoto($type, $photo) {
    return $this->{$type}()->associate($photo)->save();
  }

  public function getEmployees() {
    return User::where('employer_id', '=', $this->id)->get();
  }

  public function scopeFilter($query, $filters) {
    return $filters->apply($query);
  }

  public function facebookConnected() {
    if ((!$this->fb_page_id) && (!$this->fb_app_id)) {
      return false;
    } else {
      return true;
    }
  }

  public function instagramConnected() {
    if ($this->insta_account_token) {
      return true;
    } else {
      return false;
    }
  }

  public function squareConnected() {
    if ($this->square_token) {
      return true;
    } else {
      return false;
    }
  }

  public function qboConnected() {
    if ($this->connected_qb) {
      return true;
    } else{
      return false;
    }
  }

  public function facebookUpdate($action) {
    if ($action == "enable") {
      $this->enableFacebook();
    } else {
      $this->disableFacebook();
    }
    return $this;
  }

  public function instagramUpdate($action) {
    if ($action == "disable") {
      $this->disableInstagram();
    } 
    return $this;
  }

  public function squareUpdate($action, $feature) {
    if ($feature == "inventory") {
      $result = $this->enableSquareInventory();
    } else {
      if ($action == 'enable') {
        $result = $this->enableSquarePockeytLite();
      } else {
        $result = $this->disableSquarePockeytLite();
      }
    }
    return $result;
  }

  public function qboUpdate($action) {
    if ($action == 'disable') {
      $result = $this->disableQbo();
    } else{
      $result = $this->setTaxRateQbo();
    }
    return $result;
  }

  public function enableFacebook() {
    FacebookAccount::subscribe($this);
  }

  public function enableSquareInventory() {
    return SquareAccount::enableLocation($this);
  }

  public function enableSquarePockeytLite() {
    return SquareAccount::enablePockeytLite($this);
  }

  public function disableFacebook() {
    FacebookAccount::unSubscribe($this);
  }

  public function disableInstagram() {
    InstagramAccount::unSubscribe($this);
  }

  public function disableSquarePockeytLite() {
    return SquareAccount::disablePockeytLite($this);
  }

  public function disableQbo() {
    return QuickbookAccount::disable($this);
  }

  public function receiveFbData($hasCode) {
    if (! $hasCode) return;
    $result = FacebookAccount::getData();
    if ($result == 'success') {
      return $this;
    }
  }

  public function receiveInstaData($hasCode) {
    if (! $hasCode) return;
    $result = InstagramAccount::getData();
    if ($result == 'success') {
      return $this;
    }
  }

  public function receiveSquareData($squareData) {
    if ($squareData['state'] != env('SQUARE_STATE')) return;
    $result = SquareAccount::getData($squareData['code']);
    if ($result == 'success') {
      return $this;
    }
  }

  public function withAccountandTax() {
    return $this->with('account', 'tax')->first();
  }

  public function withTax() {
    return $this->with('tax')->first();
  }

  public function updateUsersPockeytLite($user, $type) {
    if ($type == 'enter') {
      PockeytLite::addUser($this, $user);
    } else {
      PockeytLite::removeUser($this, $user);
    }
  }

  public function receiveQuickbooksData() {
    return QuickbookAccount::getData($this);
  }

  public function setTaxRateQbo() {
    return QuickbookAccount::setTaxRate($this);
  }

  public function getFacebookEvents() {
    $client = new Client(['base_uri' => 'https://graph.facebook.com/v2.8/']);
      try {
        $response = $client->request('GET', $this->fb_page_id . '/events', [
          'query' => ['time_filter' => 'upcoming', 'access_token' => $this->fb_app_id]
        ]);
      } catch (GuzzleException $e) {
        if ($e->hasResponse()) {
          dd($e->getResponse());
        }
      }
      $data = json_decode($response->getBody());
      $events = $data->data;
      return $events;
  }

  public function setApprovedAttribute($approved) {
    $this->attributes['approved'] = filter_var($approved, FILTER_VALIDATE_BOOLEAN);
  }

  public function accountRoute() {
    return $this->account->slug;
  }

  public function createOrUpdatePosAccount($requestData) {
    \Log::debug("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
    \Log::debug($requestData);
    if ($connectedPos = $this->connectedPos) {
      $connectedPos->update($requestData);
    } else {
      $connectedPos = $this->connectedPos()->save(new connectedPos($requestData));
      $connectedPos->createPockeytCustomersCategory();
    }
  }
}
