<?php

namespace App\Listeners;

use App\Events\BillPushSuccess;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyBillPushSuccess
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
     * @param  BillPushSuccess  $event
     * @return void
     */
    public function handle(BillPushSuccess $event)
    {
        //
    }
}
