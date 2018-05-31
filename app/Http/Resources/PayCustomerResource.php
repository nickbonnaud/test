<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PayCustomerResource extends Resource
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
    $deal = $this->getDeal($user, $profile);
    if ($deal) {
      $deal['deal_item'] = $deal->deal->deal_item;
    }
    $lastPostInteractions = $this->getLastPostInteractions($user, $profile);
    if ( $lastPostInteractions) {
      $lastPostInteractions->post['post_image_url'] = $lastPostInteractions->post->api_thumbnail_url;
    }
    $loyaltyCard = $this->getLoyaltyCard($user, $profile);

    return [
      'id' => $user->id,
      'first_name' => $user->first_name,
      'last_name' => $user->last_name,
      'photo_path' => $user->photo->api_thumbnail_url,
      'large_photo_path' => $user->photo->api_url,

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
      ->latest('updated_at')
      ->select('products', 'updated_at as purchased_on', 'tax', 'tips', 'total')->first();
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
      ->where('refund_full', '=', false)
      ->select('id as deal_id')->first();
  }

  public function getLoyaltyCard($user, $profile) {
    if ($loyaltyProgram = $profile->loyaltyProgram) {
      $loyaltyCard = $user->loyaltyCards()
      ->where('loyalty_program_id', '=', $loyaltyProgram->id)
      ->select('id as loyalty_card_id', 'unredeemed_rewards', 'rewards_achieved')->first();
      if ($loyaltyCard) {
        $loyaltyCard['loyalty_reward'] = $loyaltyProgram->reward;
      }
      return $loyaltyCard;
    } else {
      return null;
    }
  }

  public function getLastPostInteractions($user, $profile) {
    return $user->postAnalytics()->where('profile_id', '=', $profile->id)
      ->latest('updated_at')
      ->select('updated_at as viewed_on')
      ->with(['post' => function($query) {
      	$query->select('is_redeemable', 'event_date', 'message', 'title', 'body');
      }])->first();
  }
}
