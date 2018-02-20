<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerRedeemDeal extends Notification
{
  use Queueable;

  public $transaction;
  public $deal;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($transaction)
  {
    $this->transaction = $transaction;
    $this->deal = $transaction->deal;
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
    $category = 'redeem_deal';
    $businessName = $this->transaction->profile->business_name;
    $businessSlug = $this->transaction->profile->slug;
    $businessId = $this->transaction->profile->id;
    $dealItem = $this->deal->deal_item;
    $title = 'Redeem your ' . $dealItem . ' from ' . $businessName . ' now?';
    $transactionId = $this->transaction->id;
    
    if (strtolower($notifiable->pushToken->device) == 'ios') {
      return [
        'aps' => [
          'alert' => [
            'title' => $title,
            'body' => 'Please swipe left or down to show options for redeeming your deal.'
          ],
          'sound' => 'default'
        ],
        'extraPayLoad' => [
          'category' => $category,
          'custom' => [
            'transactionId' => $transactionId,
            'businessName' => $businessName,
            'businessSlug' => $businessSlug,
            'businessId' => $businessId,
            'dealItem' => $dealItem
          ]
        ]
      ];
    } else {
      return [
        'data' => [
          'customTitle' => $title,
          'customMessage' => 'Please swipe down to show options for redeeming your deal.',
          'sound' => 'default',
          'category' => $category,
          "force-start" => 1,
          'content-available' => 1,
          'no-cache' => 1,
          'custom' => [
            'transactionId' => $transactionId,
            'businessName' => $businessName,
            'businessSlug' => $businessSlug,
            'businessId' => $businessId,
            'dealItem' => $dealItem
          ]
        ]
      ];
    }
  }
}
