<?php

namespace App\Listeners;

use App\Events\TransactionsChange;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateTransactions
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
     * @param  TransactionsChange  $event
     * @return void
     */
    public function handle(TransactionsChange $event)
    {
        //
    }
}
