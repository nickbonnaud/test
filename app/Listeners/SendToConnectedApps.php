<?php

namespace App\Listeners;

use App\Events\UpdateConnectedApps;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendToConnectedApps
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
     * @param  UpdateConnectedApps  $event
     * @return void
     */
    public function handle(UpdateConnectedApps $event)
    {
        //
    }
}
