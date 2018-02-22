<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AutoPayNotification extends Notification
{
  use Queueable;

  public $transaction;

  public function __construct($transaction, $reason)
  {
    $this->transaction = $transaction;
    $this->reason = $reason;
  }

  public function via($notifiable)
  {
    return ['database'];
  }

  public function toArray($notifiable)
  {
    $total = number_format($this->transaction->total / 100, 2);
    dd($total);
    $businessName = $this->transaction->profile->business_name;
    $businessSlug = $this->transaction->profile->slug;
    $transactionId = $this->transaction->id;
    $category = 'auto_pay';
    $title = 'Pockeyt Pay';
    $reasonCharged = $this->reason == 'no_response_error' ? 'not resolving your disputed bill.' : 'exiting their location and not indicating you would like to keep your bill open.';
    $inAppBody = 'You have automatically been charged $' . $total . ' by ' . $businessName . ' after ' . $reasonCharged;
    
    if (strtolower($notifiable->pushToken->device) == 'ios') {
      return [
        'aps' => [
          'alert' => [
            'title' => $title,
            'body' => $inAppBody
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
          'customMessage' =>  $inAppBody,
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
