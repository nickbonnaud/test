<?php

namespace App\Listeners;

use App\Events\TransactionError;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyError
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
     * @param  TransactionError  $event
     * @return void
     */
    public function handle(TransactionError $event)
    {
        //
    }
}
