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
    $total = round($this->transaction->total / 100, 2);
    $businessName = $this->transaction->profile->business_name;
    $businessLogo = $this->transaction->profile->logo->url;
    $category = 'payment';
    $locKey = '1';
    $transactionId = $this->transaction->id;
    $businessId = $this->transaction->profile->id;
    $businessSlug = $this->transaction->profile->slug;
    
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
            'businessSlug' => $businessSlug,
            'businessId' => $businessId,
          ]
        ]
      ];
    } else {
      return [
        'data' => [
          'title' => 'Pockeyt Pay',
          'body' => 'You have been charged $' . $total . ' by ' . $businessName . '. Please swipe down if payment options not visible.',
          'sound' => 'default',
          'category' => $category,
          "force-start" => 1,
          'content-available' => 1,
          'no-cache' => 1,
          'actions' => [
            (object) [
              'title' => 'CONFIRM',
              'callback' => 'acceptCharge',
              'foreground' => false
            ],
            (object) [
              'title' => 'REJECT',
              'callback' => 'declineCharge',
              'foreground' => true
            ],
            (object) [
              'title' => 'CUSTOM TIP',
              'callback' => 'changeTip',
              'foreground' => true
            ]
          ],
          'custom' => [
            'transactionId' => $transactionId,
            'businessSlug' => $businessSlug,
            'businessId' => $businessId,
          ]
        ]
      ];
    }
  }
}
