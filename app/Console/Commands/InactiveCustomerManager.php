<?php

namespace App\Console\Commands;

use App\UserLocation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InactiveCustomerManager extends Command
{
  protected $signature = 'app:inactive_customer_manager';
  protected $description = 'Remove inactive customers in location, send corresponding notifications, complete transactions';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $userLocations = UserLocation::where('updated_at', '<=', Carbon::now()->subMinutes(20))->get();

    foreach ($userLocations as $userLocation) {
      if ($transaction = $userLocation->checkForUnpaidTransactionOnDelete()) {
        if ($lastNotification = self::getLastNotification($transaction)) {
          self::sendNotificationOrPay($lastNotification, $transaction, $userLocation);
        } else {
          if ($transaction->status == 2) {
            $transaction->sendFixTransactionNotification(0);
          } else {
            $transaction->sendPayOrKeepOpenNotification();
          }
        }
      } else {
        $userLocation->removeLocationNoUnpaidTransaction();
      }
    }
  }

  public static function getLastNotification($transaction) {
    $notification = $transaction->user->notifications()->where('data->data->custom->transactionId', $transaction->id)->first();


    // Testing Remove Before Production
    if (!$notification) {
      $notificationLast = $transaction->user->notifications()->first();
      if ($notificationLast) {
        if ($notificationLast->data['data']['custom']['transactionId'] == $transaction->id) {
          $notification = $notificationLast;
        }
      }
    }
    return $notification;
  }

  public static function sendNotificationOrPay($lastNotification, $transaction, $userLocation) {
    switch (str_replace_first("App\\Notifications\\",'', $lastNotification->type)) {
      case 'FixTransactionNotification':
        self::fixTransactionOrPay($lastNotification, $transaction, $userLocation);
        break;
      case 'PayOrKeepOpenNotification':
        $transaction->processCharge();
        $userLocation->delete();
        break;
      case 'TransactionBillWasClosed':
        $transaction->sendPayOrKeepOpenNotification();
        break;
    }
  }

  public static function fixTransactionOrPay($lastNotification, $transaction, $userLocation) {
    $previousNotifCount = $transaction->user->notifications()
      ->where('data->data->custom->transactionId', $transaction->id)
      ->where('type', $lastNotification->type)->count();


    if ($previousNotifCount == 0) {
      $previousNotifs = $transaction->user->notifications()
        ->where('type', $lastNotification->type)->get();
      $previousNotifCount = 0;
      foreach ($previousNotifs as $previousNotif) {
        if ($previousNotif->data['data']['custom']['transactionId'] == $transaction->id) {
          $previousNotifCount = $previousNotifCount + 1;
        }
      }
    }
    switch ($previousNotifCount) {
      case 1:
      case 2:
      case 3:
        $transaction->sendFixTransactionNotification($previousNotifCount);
        break;
      case 4:
        $transaction->processCharge();
        $userLocation->delete();
    }
  }
}
