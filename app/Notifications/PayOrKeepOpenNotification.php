<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PayOrKeepOpenNotification extends Notification
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
    $businessSlug = $this->transaction->profile->slug;
    $category = 'pay_or_keep';
    $transactionId = $this->transaction->id;
    $title = 'Pay bill or keep open?';
    $inAppBody = 'All done at ' . $businessName . '? Pay your total of $' . $total . ' by tapping the Pay button on this notification or let Pockeyt automatically pay for you. Not done? Tap the Keep Open button to avoid Pockeyt automatically closing your bill.';
    
    if (strtolower($notifiable->pushToken->device) == 'ios') {
      return [
        'notification' => [
          'title' => $title,
          'body' => 'Please swipe left or down to view options. ' . $inAppBody,
          'sound' => 'default',
          'click-action' => $category,
        ],
        'data' => [
          'transactionId' => $transactionId,
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
          'customMessage' =>  'Please swipe down if options not visible. ' . $inAppBody,
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
