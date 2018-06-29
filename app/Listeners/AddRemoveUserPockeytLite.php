<?php

namespace App\Listeners;

use App\Events\CustomerBreakGeoFence;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddRemoveUserPockeytLite
{
  /**
   * Create the event listener.
   *
   * @return void
   */
  public function __construct()
  {
      //
  }

  /**
   * Handle the event.
   *
   * @param  CustomerBreakGeoFencePockeytLite  $event
   * @return void
   */
  public function handle(CustomerBreakGeoFence $event)
  {
    $userLocation = $event->userLocation;
    $profile = $event->profile;
    $user = $event->user;
    $type = $event->type;

    dd('TADA');
    if ($profile->account->pockeyt_lite_enabled) {
      $profile->updateUsersPockeytLite($event->user, $event->type);
    } elseif ($connectedPos = $profile->connectedPos()) {
      if ($connectedPos->account_type == 'clover') {
        $connectedPos->createDeleteCustomer($userLocation);
      }
    }
  }
}
