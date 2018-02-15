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
    $locKey = '1';
    $title = 'Redeem your ' . $this->loyaltyProgram->reward . ' now?';
    $loyaltyCardId = $this->loyaltyCard->id;
    $unredeemedRewards = $this->loyaltyCard->unredeemed_rewards--;
    $inAppMessage = 'Redeem your ' . $this->loyaltyProgram->reward . ' from ' . $this->loyaltyProgram->profile->business_name . ' now?';
    
    if (strtolower($notifiable->pushToken->device) == 'ios') {
      return [
        'aps' => [
          'alert' => [
            'title' => $title,
            'body' => 'Please swipe left or down to show options for redeeming your loyalty reward from ' . $this->loyaltyProgram->profile->business_name . '.'
          ],
          'sound' => 'default'
        ],
        'extraPayLoad' => [
          'category' => $category,
          'locKey' => $locKey,
          'custom' => [
            'loyaltyCardId' => $loyaltyCardId,
            'unredeemed_rewards' => $unredeemedRewards,
            'inAppMessage' => $inAppMessage,
          ]
        ]
      ];
    } else {
      return [
        'data' => [
          'title' => $title,
          'body' => 'Please swipe down to show options for redeeming your loyalty reward from ' . $this->loyaltyProgram->profile->business_name . '.',
          'sound' => 'default',
          'category' => $category,
          "force-start" => 1,
          'actions' => [
            (object) [
              'title' => 'REDEEM',
              'callback' => 'redeemReward',
              'foreground' => true
            ],
            (object) [
              'title' => 'REJECT',
              'callback' => 'declineRedeemReward',
              'foreground' => true
            ]
          ],
          'custom' => [
            'loyaltyCardId' => $loyaltyCardId,
            'unredeemed_rewards' => $unredeemedRewards,
          ]
        ]
      ];
    }
  }
}
