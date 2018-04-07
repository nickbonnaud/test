<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerRedeemReward extends Notification
{
  use Queueable;

  public $loyaltyCard;
  public $loyaltyProgram;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($loyaltyCard, $loyaltyProgram)
  {
    $this->loyaltyCard = $loyaltyCard;
    $this->loyaltyProgram = $loyaltyProgram;
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
    $category = 'redeem_reward';
    $title = 'Redeem your ' . $this->loyaltyProgram->reward . ' now?';
    $loyaltyCardId = $this->loyaltyCard->id;
    $businessName = $this->loyaltyProgram->profile->business_name;
    $inAppBody = 'Please redeem your loyalty reward from ' . $businessName . '.';
    
    if (strtolower($notifiable->pushToken->device) == 'ios') {
      return [
        'notification' => [
          'title' => $title,
          'body' => 'Please swipe left or down to show options for redeeming your loyalty reward from ' . $businessName . '.',
          'click-action' => $category,
          'sound' => 'default'
        ],
        'data' => [
          'loyaltyCardId' => $loyaltyCardId,
          'businessName' => $businessName,
          'inAppBody' => $inAppBody,
          'transactionId' => null,
          'notId' => 1,
          'category' => $category,
        ],
        'priority' => 'high'
      ];
    } else {
      return [
        'data' => [
          'customTitle' => $title,
          'customMessage' => 'Please swipe down to show options for redeeming your loyalty reward from ' . $businessName . '.',
          'sound' => 'default',
          'category' => $category,
          "force-start" => 1,
          'content-available' => 1,
          'no-cache' => 1,
          'custom' => [
            'loyaltyCardId' => $loyaltyCardId,
            'businessName' => $businessName,
            'inAppBody' => $inAppBody,
            'transactionId' => null
          ]
        ]
      ];
    }
  }
}
