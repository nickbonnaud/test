<?php

namespace App\Listeners;

use App\Events\CustomerBillUpdate;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUpdatedBillToCustomer
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
     * @param  CustomerBillUpdate  $event
     * @return void
     */
    public function handle(CustomerBillUpdate $event)
    {
        //
    }
}
