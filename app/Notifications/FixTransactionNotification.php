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
    $pluralizedTerm = $this->previousNotifCount == 1 ? 'notification' : 'notifications';
    $total = number_format(round($this->transaction->total / 100, 2), 2);
    $businessName = $this->transaction->profile->business_name;
    $businessSlug = $this->transaction->profile->slug;
    $businessPhoneNumber = $this->transaction->profile->account->phone;
    $category = 'payment_rejected';
    $transactionId = $this->transaction->id;
    $title = 'Please settle your bill with ' . $businessName . '.';
    $inAppBody = 'Please resolve your bill dispute with ' . $businessName . '. Swipe left or down to view options. Failure to resolve your dispute will result in the automatic charge of your total bill, $' . $total . ' plus your default tip. You will be sent ' . (2 - $this->previousNotifCount) . ' more ' . $pluralizedTerm . ' before you are charged.';
    
    if (strtolower($notifiable->pushToken->device) == 'ios') {
      return [
        'notification' => [
          'title' => $title,
          'body' => $inAppBody,
          'sound' => 'default',
          'click-action' => $category,
        ],
        'data' => [
          'transactionId' => $transactionId,
          'businessName' => $businessName,
          'businessSlug' => $businessSlug,
          'phoneNumber' => $businessPhoneNumber,
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
