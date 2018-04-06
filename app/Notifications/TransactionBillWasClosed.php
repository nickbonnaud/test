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
    $total = number_format(round($this->transaction->total / 100, 2), 2);
    $businessName = $this->transaction->profile->business_name;
    $category = 'payment';
    $transactionId = $this->transaction->id;
    $businessSlug = $this->transaction->profile->slug;
    $inAppBody = 'You have been charged $' . $total . ' by ' . $businessName . '.';
    $title = 'Pockeyt Pay';
    
    if (strtolower($notifiable->pushToken->device) == 'ios') {
      return [
        'notification' => [
          'title' => $title,
          'body' => 'Please swipe this notification left and tap VIEW to see options. You have been charged $' . $total . ' by ' . $businessName . '.',
          'click-action' => $category,
          'sound' => 'default'
        ],
        'data' => [
          'transactionId' => $transactionId,
          'businessName' => $businessName,
          'businessSlug' => $businessSlug,
          'inAppBody' => $inAppBody,
          'category' => $category,
          'notId' => 1
        ]
      ];
    } else {
      return [
        'data' => [
          'customTitle' => $title,
          'customMessage' => 'You have been charged $' . $total . ' by ' . $businessName . '. Please swipe down if payment options not visible.',
          'sound' => 'default',
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
