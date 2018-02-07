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

  public function __construct($transaction) {
    $this->transaction = $transaction;
  }

  public function broadcastWith() {
    return [
      'transaction' => [
        'id' => $this->transaction->id,
        'business_name' => $this->transaction->profile->business_name,
        'logo' => $this->transaction->profile->logo->apiUrl,
        'products' => $this->transaction->products,
        'tax' => $this->transaction->tax,
        'tips' => $this->transaction->tips,
        'net_sales' => $this->transaction->net_sales,
        'total' => $this->transaction->total,
        'purchased_on' => $this->transaction->created_at,
        'status' => $this->transaction->status,
        'bill_closed' => $this->transaction->bill_closed
      ]
    ];
  }

  public function broadcastOn() {
    return new PrivateChannel('customer.' . $this->transaction->user_id);
  }
}
