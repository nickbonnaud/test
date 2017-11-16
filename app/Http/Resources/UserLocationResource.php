<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserLocationResource extends Resource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request
   * @return array
   */
  public function toArray($request) {
    $user = $this->user;

    $lastTransaction = $user->transactions()->where('profile_id', '=', $this->profile_id)->where('paid', '=', true)->where('refund_full', '=', false)->whereNull('deal_id')->latest('updated_at')->first();

    $openBill = $user->transactions()->where('profile_id', '=', $this->profile_id)->where('paid', '=', false)->latest('updated_at')->first();

    $deal = $user->transactions()->where('profile_id', '=', $this->profile_id)->where('paid', '=', true)->whereNotNull('deal_id')->where('redeemed', '=', false)->where('refund_full', '=', false)->first();

    $lastPostInteractions = $user->postAnalytics()->where('profile_id', '=', $this->profile_id)->latest('updated_at')->with('post.photo')->first();

    return [
      'id' => $user->id,
      'first_name' => $user->first_name,
      'last_name' => $user->last_name,
      'photo_path' => $user->photo_path,
      'last_transaction' => $lastTransaction,
      'open_bill' => $openBill,
      'last_post_interactions' => $lastPostInteractions,
      'deal_data' => $deal
    ];
  }
}
