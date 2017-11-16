<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerEnterGeoFence extends Notification
{
  use Queueable;

  public $profile;

  public function __construct($profile)
  {
    $this->profile = $profile;
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
    $title = 'Pockeyt Pay Available';
    $body = 'Pockeyt Pay available for ' . $this->profile->business_name . '. Just say you are paying with Pockeyt!';
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
        'notification' => [
          'title' => $title,
          'body' => $body,
          'sound' => 'default'
        ],
        'data' => [
          'category' => $category,
          'locKey' => $locKey,
          'custom' => [
            'inAppMessage' => $body
          ]
        ]
      ];
    }
  }
}
