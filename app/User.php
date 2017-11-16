<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
  use Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'first_name',
    'last_name',
    'email',
    'password',
    'role',
    'on_shift',
    'employer_id'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token', 'is_admin'
  ];

  protected $casts = [
      'is_admin' => 'boolean'
  ];

  public function getJWTIdentifier() {
    return $this->getKey();
  }

   public function getJWTCustomClaims() {
    return [];
  }

  public function owns($relation) {
    return $relation->user_id == $this->id;
  }

  public function profile() {
    return $this->hasOne(Profile::class);
  }

  public function photo() {
    return $this->belongsTo('App\Photo');
  }

  public function pushToken() {
    return $this->hasOne(PushToken::class);
  }

  public function locations() {
    return $this->hasMany('App\Location');
  }

  public function transactions() {
    return $this->hasMany('App\Transaction');
  }

  public function loyaltyCards() {
    return $this->hasMany('App\LoyaltyCard');
  }

  public function postAnalytics() {
    return $this->hasMany('App\PostAnalytics');
  }

  public function invites() {
    return $this->hasMany('App\Invite');
  }

  public function publish(Profile $profile, $county, $state) {
    $profile = $profile->addTaxRate($county, $state);
    $this->profile()->save($profile);
    $this->setRole($profile);
    return $profile;
  }
  
  public function setRole($profile) {
    $this->employer_id = $profile->id;
    $this->save();
  }

  public function scopeFilter($query, $filters, $profile = null) {
    if ($profile) {
      return $filters->apply($query)->where('employer_id', '=', $profile->id);
    } else {
      return $filters->apply($query);
    }
  }

  public function setOnShiftAttribute($onShift) {
    $this->attributes['on_shift'] = filter_var($onShift, FILTER_VALIDATE_BOOLEAN);
  }

  public function recentNotificationsByType($type) {
    $this->type = $type;
    return $notifications = ($this->notifications)->filter(function($notif) {
      return str_replace_first("App\\Notifications\\",'', $notif->type) == $this->type;
    })->all();
  }

  public function updateData($userData, $file) {
    $this->updatePassword($userData);
    $this->updateEmailName($userData);
    $this->updatePhoto($file);
    $this->save();
    return $this;
  }

  public function updateEmailName($userData) {
    if (array_key_exists('email', $userData) && array_key_exists('first_name', $userData) && array_key_exists('last_name', $userData)) {
      $this->email = $userData['email'];
      $this->first_name = $userData['first_name'];
      $this->last_name = $userData['last_name'];
    }
  }

  public function updatePassword($userData) {
    if (array_key_exists('password', $userData)) {
      $this->password = Hash::make($userData['password']);
    }
  }

  public function updatePhoto($file) {
    if ($file) {
      $photo = Photo::fromForm($file);
      $photo->save();
      $this->deleteOldPhoto();
      $this->photo()->associate($photo);
    }
  }

  public function deleteOldPhoto() {
    if ($photo = $this->photo) {
      $this->photo()->dissociate()->save();
      $photo->delete();
    }
  }
}
