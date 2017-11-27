<?php

namespace App\Listeners;

use App\Events\CustomerRedeemItem;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyBusinessCustomerRedeemItem
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
     * @param  CustomerRedeemItem  $event
     * @return void
     */
    public function handle(CustomerRedeemItem $event)
    {
        //
    }
}
