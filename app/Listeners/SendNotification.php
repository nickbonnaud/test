<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;

use App\Events\BillPushSuccess;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class SendNotification
{

  /**
   * Create the event listener.
   *
   * @return void
   */
  public function __construct()
  {
    $this->notificationTypes = ['TransactionBillWasClosed', 'LoyaltyRewardEarned', 'CustomerRedeemReward', 'CustomerRedeemDeal'];
  }

  /**
   * Handle the event.
   *
   * @param  NotificationSent  $event
   * @return void
   */
  public function handle(NotificationSent $event)
  {
    $notification = $this->getNotification($event);
    $pushToken = $event->notifiable->pushToken;
    $response = $this->sendPush($notification, $pushToken);
    $success = $this->checkSuccess($response, $pushToken);
    $type = str_replace_first("App\\Notifications\\",'', $notification->type);
    
    foreach ($this->notificationTypes as $notificationType) {
      if ($notificationType == $type && method_exists($this, $notificationType)) {
        $this->$notificationType($event, $notification, $success);
      }
    }
  }

  public function sendPush($notification, $pushToken) {
    $pushService = strtolower($pushToken->device) === 'ios' ? 'apn' : 'fcm';

    if ($pushService === 'fcm') {
      $this->sendFcm($notification, $pushToken);
    } else {
      $push = \PushNotification::setService($pushService)
        ->setMessage($notification->data)
        ->setDevicesToken($pushToken->push_token);

      if ($pushService === 'fcm') {
        $push->setApiKey(env('FCM_SERVER_KEY'));
      }
      return $push->send()->getFeedback();
    }
  }

  public function sendFcm($notification, $pushToken) {
    $notificationBuilder = new PayloadNotificationBuilder($notification->data['notification']['title']);
    $notificationBuilder
      ->setBody($notification->data['notification']['body'])
      ->setSound('default');

    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData(['actions' => $notification->data['data']['actions']]);

    $notification = $notificationBuilder->build();
    $data = $dataBuilder->build();
    $token = $pushToken->push_token;

    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

    Log::info($downstreamResponse->numberSuccess());
    Log::info($downstreamResponse->numberFailure());
    Log::info($downstreamResponse->numberModification());
  }

  public function checkSuccess($response, $pushToken) {
    if (strtolower($pushToken->device) === 'ios') {
      return $response->getCode() === 0;
    } else {
      return $response->success === 1;
    }
  }

  public function getNotification($event) {
    $this->notifId = $event->notification->id;
    return $notification = ($event->notifiable->unreadNotifications)->filter(function($notif) {
      return $notif->id = $this->notifId;
    })->first();
  }

  public function TransactionBillWasClosed($event, $notification, $success) {
    $transaction = $event->notification->transaction;
    $transaction->notification_id = $notification->id;
    if ($success) {
      $transaction->status = 11;
    } else {
      $transaction->status = 0;
      $transaction->transactionErrorEvent();
    }
    $transaction->save();
    $transaction->transactionChangeEvent();
    event(new BillPushSuccess($transaction->profile->slug, $success));
  }

  public function LoyaltyRewardEarned($event, $notification, $success) {
    if ($success) {
      $notification->delete();
    }
  }

  public function CustomerRedeemReward($event, $notification, $success) {
    if ($success) {
      $notification->delete();
    }
  }

  public function CustomerRedeemDeal($event, $notification, $success) {
    if ($success) {
      $notification->delete();
    }
  }
}
