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

    $lastTransaction = $this->getLastTransaction();
    $openBill = $this->getOpenBill();
    $deal = $this->getDeal();
    $lastPostInteractions = $this->getLastPostInteractions();
    $loyaltyCard = $this->getLoyaltyCard();

    return [
      'id' => $user->id,
      'first_name' => $user->first_name,
      'last_name' => $user->last_name,
      'photo_path' => $user->photo_path,
      'last_transaction' => $lastTransaction,
      'open_bill' => $openBill,
      'last_post_interactions' => $lastPostInteractions,
      'deal_data' => $deal,
      'loyalty_card' => $loyaltyCard
    ];
  }

  public function getLastTransaction() {
    return $this->user->transactions()->where('profile_id', '=', $this->profile_id)
      ->where('paid', '=', true)
      ->where('refund_full', '=', false)
      ->whereNull('deal_id')
      ->latest('updated_at')->first();
  }

  public function getOpenBill() {
    return $this->user->transactions()->where('profile_id', '=', $this->profile_id)
      ->where('paid', '=', false)
      ->latest('updated_at')->first();
  }

  public function getDeal() {
    return $this->user->transactions()->where('profile_id', '=', $this->profile_id)
      ->where('paid', '=', true)
      ->whereNotNull('deal_id')
      ->where('redeemed', '=', false)
      ->where('refund_full', '=', false)->first();
  }

  public function getLoyaltyCard() {
    if ($loyaltyProgram = $this->profile->loyaltyProgram) {
      $loyaltyCard = $this->user->loyaltyCards()->where('loyalty_program_id', '=', $loyaltyProgram->id)->first();
      $loyaltyCard['reward'] = $loyaltyProgram->reward;
      return $loyaltyCard;
    } else {
      return null;
    }
  }

  public function getLastPostInteractions() {
    return $this->user->postAnalytics()->where('profile_id', '=', $this->profile_id)
      ->latest('updated_at')
      ->with('post.photo')->first();
  }
}
