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

  /**
   * Create a new notification instance.
   *
   * @return void
   */
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
    $category = 'pay_or_keep';
    $locKey = '1';
    $transactionId = $this->transaction->id;
    $businessId = $this->transaction->profile->id;
    $title = 'Pay bill or keep open?';
    $inAppMessage = 'Either you have left ' . $businessName . ' or you have closed the Pockeyt app and Pockeyt cannot determine your location. Please pay your bill of $' . $total . ', reopen the Pockeyt app, or keep this bill open. You will be automatically charged if you do not respond to this notification.';
    
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
          'locKey' => $locKey,
          'custom' => [
            'transactionId' => $transactionId,
            'businessId' => $businessId,
            'inAppMessage' => $inAppMessage,
            'businessLogo'=> $businessLogo,
          ]
        ]
      ];
    } else {
      return [
        'data' => [
          'title' => $title,
          'body' =>  'Please swipe down if options not visible. ' . $inAppMessage,
          'sound' => 'default',
          'category' => $category,
          "force-start" => 1,
          'actions' => [
            (object) [
              'title' => 'CONFIRM',
              'callback' => 'acceptCharge',
              'foreground' => true
            ],
            (object) [
              'title' => 'CUSTOM TIP',
              'callback' => 'changeTip',
              'foreground' => true
            ],
             (object) [
              'title' => 'KEEP OPEN',
              'callback' => 'keepBillOpen',
              'foreground' => true
            ]
          ],
          'custom' => [
            'transactionId' => $transactionId,
            'businessId' => $businessId,
          ]
        ]
      ];
    }
  }
}
