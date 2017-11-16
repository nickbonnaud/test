<?php

namespace App\Listeners;

use App\Events\CustomerBreakGeoFence;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddRemoveUser
{
  /**
   * Create the event listener.
   *
   * @return void
   */
  public function __construct()
  {
    
  }

  /**
   * Handle the event.
   *
   * @param  CustomerBreakGeoFence  $event
   * @return void
   */
  public function handle(CustomerBreakGeoFence $event)
  {
    
  }
}
