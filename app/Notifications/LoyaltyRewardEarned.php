<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoyaltyRewardEarned extends Notification
{
  use Queueable;

  public $loyaltyProgram;
  public $rewardsQuantity;

  public function __construct($loyaltyProgram, $rewardsQuantity)
  {
    $this->loyaltyProgram = $loyaltyProgram;
    $this->rewardsQuantity = $rewardsQuantity;
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
    $businessName = $this->loyaltyProgram->profile->business_name;

    if ($this->rewardsQuantity > 1) {
      $title = 'You just earned ' . $this->rewardsQuantity . ' ' . str_plural($this->loyaltyProgram->reward) . '!';
    } else {
      $title = 'You just earned a ' . $this->loyaltyProgram->reward . '!';
    }
    $body = 'Redeem your ' . $this->loyaltyProgram->reward . ' at ' . $businessName . ' now or save your reward for later!';
    $category = 'default';

    if (strtolower($notifiable->pushToken->device) == 'ios') {
      return [
        'aps' => [
          'alert' => [
            'title' => $title,
            'body' => $body
          ],
          'sound' => 'default'
        ],
        'extraPayLoad' => [
          'category' => $category,
          'custom' => [
            'inAppBody' => $body
          ]
        ]
      ];
    } else {
      return [
        'data' => [
          'customTitle' => $title,
          'customMessage' => $body,
          'sound' => 'default',
          'category' => $category,
          "force-start" => 1,
          'content-available' => 1,
          'no-cache' => 1,
          'custom' => [
            'businessName' => $businessName,
            'inAppBody' => $body
          ]
        ]
      ];
    }
  }
}
