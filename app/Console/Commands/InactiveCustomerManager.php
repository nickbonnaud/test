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
    $userLocations = UserLocation::get();

    foreach ($userLocations as $userLocation) {
      if ($transaction = $userLocation->checkForUnpaidTransactionOnDelete()) {
        if ($lastNotification = self::getLastNotification($transaction)) {
          self::sendNotificationOrPay($lastNotification, $transaction, $userLocation);
        } else {
          if (($transaction->status == 2) || ($transaction->status == 3) || ($transaction->status == 4)) {
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
    return $notification;
  }

  public static function sendNotificationOrPay($lastNotification, $transaction, $userLocation) {
    switch (str_replace_first("App\\Notifications\\",'', $lastNotification->type)) {
      case 'FixTransactionNotification':
        self::fixTransactionOrPay($lastNotification, $transaction, $userLocation);
        break;
      case 'PayOrKeepOpenNotification':
        if (($transaction->status == 2) || ($transaction->status == 3) || ($transaction->status == 4)) {
          $transaction->sendFixTransactionNotification();
        } else {
          $transaction->processCharge();
          $transaction->sendAutoPayNotification('no_response_exit');
          $userLocation->delete();
        }
        break;
      case 'TransactionBillWasClosed':
        if (($transaction->status == 2) || ($transaction->status == 3) || ($transaction->status == 4)) {
          $transaction->sendFixTransactionNotification();
        } else {
          $transaction->sendPayOrKeepOpenNotification();
        }
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
        $transaction->sendFixTransactionNotification($previousNotifCount);
        break;
      case 3:
        $transaction->processCharge();
        $transaction->sendAutoPayNotification('no_response_error');
        $userLocation->delete();
        break;
    }
  }
}
