<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FixTransactionNotification extends Notification
{
  use Queueable;

  public $transaction;
  public $previousNotifCount;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($transaction, $previousNotifCount)
  {
    $this->transaction = $transaction;
    $this->previousNotifCount = $previousNotifCount;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['database'];
  }


  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    $pluralizedTerm = $this->previousNotifCount > 1 ? 'notifications' : 'notification';
    $total = round($this->transaction->total / 100, 2);
    $businessName = $this->transaction->profile->business_name;
    $businessSlug = $this->transaction->profile->slug;
    $businessPhoneNumber = $this->transaction->profile->account->phone;
    $category = 'payment_rejected';
    $transactionId = $this->transaction->id;
    $title = 'Please settle your $' . $total . ' bill with ' . $businessName . '.';
    $inAppBody = 'Please resolve your bill dispute with ' . $businessName . ' and pay your bill. Failure to resolve your dispute will result in the automatic charge of $' . $total . ' after failing to respond to 3 Bill Notifications. You have been sent ' . $this->previousNotifCount . ' ' . $pluralizedTerm . '.';
    
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
          'locKey' => $locKey,
          'custom' => [
            'transactionId' => $transactionId,
            'inAppMessage' => $inAppBody,
            'phoneNumber' => $businessPhoneNumber
          ]
        ]
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
            'businessName' => $businessName,
            'businessSlug' => $businessSlug,
            'phoneNumber' => $businessPhoneNumber,
            'inAppBody' => $inAppBody
          ]
        ]
      ];
    }
  }
}
