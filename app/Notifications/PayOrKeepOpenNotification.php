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
    dd('here');
    $total = number_format(round($this->transaction->total / 100, 2), 2);
    $businessName = $this->transaction->profile->business_name;
    $businessSlug = $this->transaction->profile->slug;
    $category = 'pay_or_keep';
    $transactionId = $this->transaction->id;
    $title = 'Pay bill or keep open?';
    $inAppBody = 'Either you have left ' . $businessName . ' or you have force closed the Pockeyt app and Pockeyt cannot determine your location. Please pay your bill of $' . $total . ' or reopen the Pockeyt app while at ' . $businessName . '. You will be automatically charged if your bill is not paid or Pockeyt is not reopened.';
    
    if (strtolower($notifiable->pushToken->device) == 'ios') {
      return [
        'aps' => [
          'alert' => [
            'title' => $title,
            'body' => 'Please swipe left or down to view options. ' . $inAppMessage
          ],
          'sound' => 'default'
        ],
        'extraPayLoad' => [
          'category' => $category,
          'custom' => [
            'transactionId' => $transactionId,
            'inAppBody' => $inAppBody,
          ]
        ]
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
