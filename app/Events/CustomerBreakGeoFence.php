<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class CustomerBreakGeoFence implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $userLocation;
  public $type;
  public $profile;
  public $user;

  /**
   * Create a new event instance.
   *
   * @return void
   */
  public function __construct($userLocation, $type)
  {
    $this->userLocation = $userLocation;
    $this->type = $type;
    $this->profile = $userLocation->profile;
    $this->user = $userLocation->user;
  }

  public function broadcastWith() {
    $lastTransaction = $this->user->transactions()->where('profile_id', '=', $this->profile->id)->where('paid', '=', true)->where('refund_full', '=', false)->whereNull('deal_id')->latest('updated_at')->first();

    $openBill = $this->user->transactions()->where('profile_id', '=', $this->profile->id)->where('paid', '=', false)->latest('updated_at')->first();

    $deal = $this->user->transactions()->where('profile_id', '=', $this->profile->id)->where('paid', '=', true)->whereNotNull('deal_id')->where('redeemed', '=', false)->where('refund_full', '=', false)->first();

    $lastPostInteractions = $this->user->postAnalytics()->where('profile_id', '=', $this->profile->id)->latest('updated_at')->with('post.photo')->first();
    
    return [
      'id' => $this->user->id,
      'first_name' => $this->user->first_name,
      'last_name' => $this->user->last_name,
      'photo_path' => $this->user->photo_path,
      'last_transaction' => $lastTransaction,
      'open_bill' => $openBill,
      'last_post_interactions' => $lastPostInteractions,
      'deal_data' => $deal,
      'type' => $this->type
    ];
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return new PrivateChannel('geofence.' . $this->userLocation->profile->slug);
  }
}
