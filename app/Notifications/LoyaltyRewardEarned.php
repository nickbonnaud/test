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
    if ($this->rewardsQuantity > 1) {
      $title = 'You just earned ' . $this->rewardsQuantity . ' ' . str_plural($this->loyaltyProgram->reward) . '!';
    } else {
      $title = 'You just earned a ' . $this->loyaltyProgram->reward . '!';
    }
    $body = 'Redeem your ' . $this->loyaltyProgram->reward . ' at ' . $this->loyaltyProgram->profile->business_name . ' now or save your reward for later!';
    $category = 'default';
    $locKey = '1';

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
          'locKey' => $locKey,
          'custom' => [
            'inAppMessage' => $body
          ]
        ]
      ];
    } else {
      return [
        'data' => [
          'title' => $title,
          'body' => $body,
          'sound' => 'default',
          'category' => $category,
        ]
      ];
    }
  }
}
