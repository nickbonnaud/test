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
    $locKey = '1';
    $title = 'Redeem your ' . $this->deal->deal_item . ' now?';
    $transactionId = $this->transaction->id;
    $inAppMessage = 'Redeem your ' . $this->deal->deal_item . ' from ' . $this->transaction->profile->business_name . ' now?';
    
    if (strtolower($notifiable->pushToken->device) == 'ios') {
      return [
        'aps' => [
          'alert' => [
            'title' => $title,
            'body' => 'Please swipe left or down to show options for redeeming your deal from ' . $this->transaction->profile->business_name . '.'
          ],
          'sound' => 'default'
        ],
        'extraPayLoad' => [
          'category' => $category,
          'locKey' => $locKey,
          'custom' => [
            'transactionId' => $transactionId,
            'inAppMessage' => $inAppMessage,
          ]
        ]
      ];
    } else {
      return [
        'data' => [
          'title' => $title,
          'body' => 'Please swipe down to show options for redeeming your deal from ' . $this->transaction->profile->business_name . '.',
          'sound' => 'default',
          'category' => $category,
          'actions' => [
            (object) [
              'title' => 'REDEEM',
              'callback' => 'redeemDeal',
              'foreground' => true
            ],
            (object) [
              'title' => 'REJECT',
              'callback' => 'declineRedeemDeal',
              'foreground' => true
            ]
          ],
          'custom' => [
            'transactionId' => $transactionId,
          ]
        ]
      ];
    }
  }
}
