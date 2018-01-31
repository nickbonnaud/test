<?php

namespace App;

use Carbon\Carbon;
use App\Events\CustomerBreakGeoFence;
use App\Notifications\CustomerEnterGeoFence;
use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model {

  protected $fillable = [
    'user_id',
    'profile_id',
    'updated_at',
    'exit_notification_sent'
  ];

  protected static function boot() {
    static::created(function ($userLocation) {
      event(new CustomerBreakGeoFence($userLocation, $type='enter'));
      $userLocation->notifyUserEnter();
    });
  }

  public static function addUserLocations($locations, $user) {
    foreach ($locations as $location) {
      self::updateOrCreate(
        ['profile_id' => $location['location_id'], 'user_id' => $user->id, 'exit_notification_sent' => false],
        ['updated_at' => Carbon::now()]
      );
    }
  }

  public static function removeUserLocations($locations, $user) {
    foreach ($locations as $location) {
      $userLocation = self::where('user_id', '=', $user->id)->where('profile_id', '=', $location['location_id'])->first();
      $userLocation->removeLocation();
    }
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

  public function removeLocation() {
    if ($transaction = $this->checkForUnpaidTransactionOnDelete()) {
      if (!$this->exit_notification_sent) {
        $this->sendPaymentNotificationByType($transaction);
        $this->exit_notification_sent = true;
        $this->save();
      }
    } else {
      $this->removeLocationNoUnpaidTransaction();
    }
  }

  public function removeLocationNoUnpaidTransaction() {
    event(new CustomerBreakGeoFence($this, $type='exit'));
    $this->delete();
  }


  public function checkForUnpaidTransactionOnDelete() {
    return $this->user->transactions->where('paid', false)->where('profile_id', $this->profile->id)->first();
  }

  public function sendPaymentNotificationByType($transaction) {
    if ($transaction->checkRecentSentNotification() == 0) {
      if ($transaction->bill_closed && ($transaction->status != 0)) {
        $transaction->sendBillClosedNotification();
      } elseif($transaction->status !== 0) {
        if ($transaction->status == 2) {
          $transaction->sendFixTransactionNotification();
        } else {
          $transaction->sendPayOrKeepOpenNotification();
        }
      }
    }
  }
}
