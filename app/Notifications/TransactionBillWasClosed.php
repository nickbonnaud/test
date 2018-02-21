<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransactionBillWasClosed extends Notification
{
  use Queueable;

  public $transaction;

  public function __construct($transaction)
  {
    $this->transaction = $transaction;
  }

  public function via($notifiable)
  {
    return ['database'];
  }

  public function toArray($notifiable)
  {
    $total = round($this->transaction->total / 100, 2);
    $businessName = $this->transaction->profile->business_name;
    $category = 'payment';
    $transactionId = $this->transaction->id;
    $businessSlug = $this->transaction->profile->slug;
    $inAppBody = 'You have been charged $' . $total . ' by ' . $businessName . '.';
    
    if (strtolower($notifiable->pushToken->device) == 'ios') {
      return [
        'aps' => [
          'alert' => [
            'title' => 'Pockeyt Pay',
            'body' => 'Please swipe left or down to view bill and pay. You have been charged $' . $total . ' by ' . $businessName . '.'
          ],
          'sound' => 'default'
        ],
        'extraPayLoad' => [
          'category' => $category,
          'custom' => [
            'transactionId' => $transactionId,
            'businessName' => $businessName,
            'businessSlug' => $businessSlug,
            'inAppBody' => $inAppBody
          ]
        ]
      ];
    } else {
      return [
        'data' => [
          'customTitle' => 'Pockeyt Pay',
          'customMessage' => 'You have been charged $' . $total . ' by ' . $businessName . '. Please swipe down if payment options not visible.',
          'category' => $category,
          "force-start" => 1,
          'content-available' => 1,
          'no-cache' => 1,
          'custom' => [
            'transactionId' => $transactionId,
            'businessName' => $businessName,
            'businessSlug' => $businessSlug,
            'inAppBody' => $inAppBody
          ]
        ]
      ];
    }
  }
}
