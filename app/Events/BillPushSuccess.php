<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BillPushSuccess implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  private $profileSlug;
  public $success;

  
  public function __construct($profileSlug, $success)
  {
    $this->profileSlug = $profileSlug;
    $this->success = $success;
  }

  public function broadcastOn()
  {
    return new PrivateChannel('bill-push-success.' . $this->profileSlug);
  }
}
