<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class AccountReadyForProcessorReview
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $account;

  public function __construct($account)
  {
    $this->account = $account;
  }
}
