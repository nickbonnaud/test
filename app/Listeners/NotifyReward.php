<?php

namespace App\Listeners;

use App\Events\CustomerEarnReward;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyReward
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
     * @param  CustomerEarnReward  $event
     * @return void
     */
    public function handle(CustomerEarnReward $event)
    {
        //
    }
}
