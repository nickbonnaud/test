<?php

namespace App\Listeners;

use App\Events\CustomerRequestBill;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShowBillRequest
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
     * @param  CustomerRequestBill  $event
     * @return void
     */
    public function handle(CustomerRequestBill $event)
    {
        //
    }
}
