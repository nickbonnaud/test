<?php

namespace App\Listeners;

use App\Events\TransactionSuccess;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifySuccess
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
     * @param  TransactionSuccess  $event
     * @return void
     */
    public function handle(TransactionSuccess $event)
    {
        //
    }
}
