<?php

namespace App;

use Carbon\Carbon;
use App\Jobs\RemoveLocation;
use App\Events\CustomerBreakGeoFence;
use App\Notifications\CustomerEnterGeoFence;
use Illuminate\Database\Eloquent\Model;
use App\Events\UpdateConnectedApps;
use App\Http\Resources\PayCustomerResource;

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
      event(new UpdateConnectedApps($userLocation->profile, "customer_enter", new PayCustomerResource($userLocation)));
      $userLocation->notifyUserEnter();
    });
  }

  public static function addRemoveLocation($profileSlug, $action, $user) {
    $profile = Profile::where('slug', $profileSlug)->first();
    $userLocation = UserLocation::where('profile_id', $profile->id)->where('user_id', $user->id)->first();
    if (!$userLocation && ($action == 'enter')) {
      UserLocation::create([
        'profile_id' => $profile->id,
        'user_id' => $user->id
      ]);
    } elseif ($userLocation && ($action == 'enter')) {
      $userLocation->touch();
    } elseif ($userLocation && ($action == 'exit')) {
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
      $expireTime = (new Carbon($this->exited_on))->addSeconds((3 * 60) - 5);
      if (($transaction->status == 11 || $transaction->bill_closed) || ($this->customer_exited && $expireTime->lt(Carbon::now()))) {

        if ($transaction->status == 11 && $transaction->bill_closed && !$this->customer_exited) {
          $transaction->autoChargeCustomer();
        } elseif (!$this->exit_notification_sent  && ($transaction->status != 11 || !$transaction->bill_closed)) {
          $this->sendPaymentNotificationByType($transaction);
          $this->exit_notification_sent = true;
          $this->save();
        }
      } elseif (!$this->customer_exited) {
        $this->customer_exited = true;
        $this->exited_on = Carbon::now();
        $this->save();
        event(new UpdateConnectedApps($this->profile, "customer_exit_unpaid", new PayCustomerResource($this)));
        RemoveLocation::dispatch($transaction)->delay(now()->addMinutes(3));
      }
    } else {
      $this->removeLocationNoUnpaidTransaction();
    }
  }

  public function removeLocationNoUnpaidTransaction() {
    event(new CustomerBreakGeoFence($this, $type='exit'));
    event(new UpdateConnectedApps($this->profile, "customer_exit_paid", new PayCustomerResource($this)));
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
        if (($transaction->status == 2) || ($transaction->status == 3) || ($transaction->status == 4)) {
          $transaction->sendFixTransactionNotification();
        } else {
          $transaction->sendPayOrKeepOpenNotification();
        }
      }
    }
  }
}
