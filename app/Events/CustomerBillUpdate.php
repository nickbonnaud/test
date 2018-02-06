<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CustomerBillUpdate implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $transaction;

  public function __construct($transaction)
  {
    $this->transaction = $transaction;
  }

  public function broadcastWith() {
    return [
      'transaction' => $this->transaction
    ];
  }

  public function broadcastOn()
  {
    return new PrivateChannel('customer.' . $transaction->user_id);
  }
}
