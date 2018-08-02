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
    // $userLocations = UserLocation::where('updated_at', '<=', Carbon::now()->subMinutes(20))->get();

    foreach ($userLocations as $userLocation) {
      if ($transaction = $userLocation->checkForUnpaidTransactionOnDelete()) {
        if ($lastNotification = self::getLastNotification($transaction)) {
          self::sendNotificationOrPay($lastNotification, $transaction, $userLocation);
        } else {
          if (($transaction->status == 2) || ($transaction->status == 3) || ($transaction->status == 4)) {
            self::fixTransactionOrPay($transaction, $userLocation);
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
    if ($transaction->user->pushToken->device == 'android') {
      $notification = $transaction->user->notifications()->where('data->data->custom->transactionId', $transaction->id)->first();
    } else {
      $notification = $transaction->user->notifications()->where('data->data->transactionId', $transaction->id)->first();
    }
    return $notification;
  }

  public static function sendNotificationOrPay($lastNotification, $transaction, $userLocation) {
    switch (str_replace_first("App\\Notifications\\",'', $lastNotification->type)) {
      case 'FixTransactionNotification':
        self::fixTransactionOrPay($transaction, $userLocation);
        break;
      case 'PayOrKeepOpenNotification':
        if (($transaction->status == 2) || ($transaction->status == 3) || ($transaction->status == 4)) {
          self::fixTransactionOrPay($transaction, $userLocation);
        } else {
          self::chargeCustomer($transaction, $userLocation);
        }
        break;
      case 'TransactionBillWasClosed':
        if (($transaction->status == 2) || ($transaction->status == 3) || ($transaction->status == 4)) {
          self::fixTransactionOrPay($transaction, $userLocation);
        } else {
          if ($userLocation->exit_notification_sent) {
            self::chargeCustomer($transaction, $userLocation);
          } else {
            $transaction->sendPayOrKeepOpenNotification();
          }
        }
        break;
    }
  }

  public static function chargeCustomer($transaction, $userLocation) {
    $transaction->autoChargeCustomer();
  }

  public static function fixTransactionOrPay($transaction, $userLocation) {
    $path = $transaction->user->pushToken->device == "ios" ? "data->data->transactionId" : "data->data->custom->transactionId";
    $previousNotifCount = $transaction->user->notifications()
        ->where($path, $transaction->id)
        ->where('type', 'App\Notifications\FixTransactionNotification')->count();
    switch ($previousNotifCount) {
      case 0:
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
