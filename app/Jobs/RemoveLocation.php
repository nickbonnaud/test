<?php

namespace App\Jobs;

use App\Transaction;
use App\UserLocation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RemoveLocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction) {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
       $userLocation = UserLocation::where('profile_id', $this->transaction->profile_id)
            ->where('user_id',  $this->transaction->user_id)
            ->first();
        if ($userLocation) {
            $userLocation->removeLocation();
        }
    }
}
