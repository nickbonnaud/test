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
    $profile = $this->profile;

    $lastTransaction = $this->getLastTransaction($user, $profile);
    if ($lastTransaction) {
      $lastTransaction->products = json_decode($lastTransaction->products);
    }
    $openBill = $this->getOpenBill($user, $profile);
    $deal = $this->getDeal($user, $profile);
    if ($deal) {
      $deal['deal_item'] = $deal->deal->deal_item;
    }
    $lastPostInteractions = $this->getLastPostInteractions($user, $profile);
    if ( $lastPostInteractions) {
      $lastPostInteractions->post->photo['api_thumbnail_url'] = $lastPostInteractions->post->api_thumbnail_url;
    }
    $loyaltyCard = $this->getLoyaltyCard($user, $profile);

    return [
      'id' => $user->id,
      'first_name' => $user->first_name,
      'last_name' => $user->last_name,
      'photo_path' => $user->photo->api_thumbnail_url,
      'large_photo_path' => $user->photo->api_url,
      'last_transaction' => $lastTransaction,
      'open_bill' => $openBill,
      'last_post_interactions' => $lastPostInteractions,
      'deal_data' => $deal,
      'loyalty_card' => $loyaltyCard
    ];
  }

  public function getLastTransaction($user, $profile) {
    return $user->transactions()->where('profile_id', '=', $profile->id)
      ->where('paid', '=', true)
      ->where('refund_full', '=', false)
      ->whereNull('deal_id')
      ->latest('updated_at')->first();
  }

  public function getOpenBill($user, $profile) {
    return $user->transactions()->where('profile_id', '=', $profile->id)
      ->where('paid', '=', false)
      ->latest('updated_at')->first();
  }

  public function getDeal($user, $profile) {
    return $user->transactions()->where('profile_id', '=', $profile->id)
      ->where('paid', '=', true)
      ->whereNotNull('deal_id')
      ->where('redeemed', '=', false)
      ->where('refund_full', '=', false)->first();
  }

  public function getLoyaltyCard($user, $profile) {
    if ($loyaltyProgram = $profile->loyaltyProgram) {
      $loyaltyCard = $user->loyaltyCards()->where('loyalty_program_id', '=', $loyaltyProgram->id)->first();
      if ($loyaltyCard) {
        $loyaltyCard['reward'] = $loyaltyProgram->reward;
      }
      return $loyaltyCard;
    } else {
      return null;
    }
  }

  public function getLastPostInteractions($user, $profile) {
    return $user->postAnalytics()->where('profile_id', '=', $profile->id)
      ->latest('updated_at')
      ->with('post.photo')->first();
  }
}
