<?php

namespace App;

use App\Events\CustomerBreakGeoFence;
use App\Notifications\CustomerEnterGeoFence;
use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model {

  protected $fillable = [
    'user_id',
    'profile_id',
    'updated_at'
  ];

  protected static function boot() {
    static::created(function ($userLocation) {
      event(new CustomerBreakGeoFence($userLocation, $type='enter'));
      $userLocation->notifyUserEnter();
    });

    static::deleting(function ($userLocation) {
      event(new CustomerBreakGeoFence($userLocation, $type='exit'));
    });
  }

  public function user() {
    return $this->belongsTo('App\User');
  }

  public function profile() {
    return $this->belongsTo('App\Profile');
  }

  public function scopeFilter($query, $filters, $profile) {
    return $filters->apply($query)->where('profile_id', '=', $profile->id);
  }

  public function notifyUserEnter() {
    if (!$this->checkIfRecentSentForLocation()) {
      $this->user->notify(new CustomerEnterGeoFence($this->profile));
    }
  }

  public function checkIfRecentSentForLocation() {
    $recentGeoFenceNotifications = $this->user->recentNotificationsByType('CustomerEnterGeoFence');
    if (count($recentGeoFenceNotifications) > 0) {
      foreach ($recentGeoFenceNotifications as $geoFenceNotification) {
        $businessName = str_after(str_before(array_get($geoFenceNotification->data, 'notification.body'), '. Just'), 'for ');
        if ($businessName == $this->profile->business_name) {
          return true;
        }
      }
    } else {
      return false;
    }
  }
}
