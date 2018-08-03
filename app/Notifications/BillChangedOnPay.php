<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class BillChangedOnPay extends Notification
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
    $category = 'bill_changed';
    $transactionId = $this->transaction->id;
    $businessSlug = $this->transaction->profile->slug;
    $inAppBody = 'Your bill at ' . $businessName . ' has changed recently. Your new total is ' . $total . '.';
    $title = 'Bill Changed';
    
    if (strtolower($notifiable->pushToken->device) == 'ios') {
      return [
        'notification' => [
          'title' => $title,
          'body' => 'Please swipe this notification left or down and tap VIEW to see options. ' . $inAppBody,
          'click-action' => $category,
          'sound' => 'default'
        ],
        'data' => [
          'transactionId' => $transactionId,
          'total' => $this->transaction->total,
          'businessName' => $businessName,
          'businessSlug' => $businessSlug,
          'inAppBody' => $inAppBody,
          'category' => $category,
          'notId' => 1
        ],
        'priority' => 'high'
      ];
    } else {
      return [
        'data' => [
          'customTitle' => $title,
          'customMessage' => $inAppBody,
          'sound' => 'default',
          'category' => $category,
          "force-start" => 1,
          'content-available' => 1,
          'no-cache' => 1,
          'custom' => [
            'transactionId' => $transactionId,
            'total' => $this->transaction->total,
            'businessName' => $businessName,
            'businessSlug' => $businessSlug,
            'inAppBody' => $inAppBody
          ]
        ]
      ];
    }
  }
}
