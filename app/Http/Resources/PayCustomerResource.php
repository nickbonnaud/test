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
    $deal = $this->getDeal($user, $profile);
    $lastPostInteractions = $this->getLastPostInteractions($user, $profile);
    $loyaltyCard = $this->getLoyaltyCard($user, $profile);

    return [
      'id' => $user->id,
      'first_name' => $user->first_name,
      'last_name' => $user->last_name,
      'photo_path' => $user->photo->api_thumbnail_url,
      'large_photo_path' => $user->photo->api_url,
      'recent_transaction' => $lastTransaction,
      'last_post_interactions' => $lastPostInteractions,
      'deal_data' => $deal,
      'loyalty_card' => $loyaltyCard
    ];
  }

  public function getLastTransaction($user, $profile) {
    $transaction = $user->transactions()->where('profile_id', '=', $profile->id)
      ->where('paid', '=', true)
      ->where('refund_full', '=', false)
      ->whereNull('deal_id')
      ->latest('updated_at')->first();

    if ($transaction) {
    	$lastTransaction = (object) [
    		'has_recent' => true,
    		'purchased_items' => json_decode($transaction->products),
    		'purchased_on' => $transaction->updated_at,
    		'tax' => $transaction->tax,
    		'tip' => $transaction->tips,
    		'total' => $transaction->total
    	];
    } else {
    	$lastTransaction = (object) [
    		'has_recent' => false
    	];
    }
    return $lastTransaction;
  }

  public function getDeal($user, $profile) {
    $deal = $user->transactions()->where('profile_id', '=', $profile->id)
      ->where('paid', '=', true)
      ->whereNotNull('deal_id')
      ->where('redeemed', '=', false)
      ->where('refund_full', '=', false)->first();

    if ($deal) {
    	$formattedDeal = (object) [
    		'has_deal' => true,
    		'deal_id' => $deal->id,
    		'deal_item' => $deal->deal->deal_item
    	];
    } else {
    	$formattedDeal = (object) [
    		'has_deal' => false
    	];
    }
    return $formattedDeal;
  }

  public function getLastPostInteractions($user, $profile) {
    $postInteractions = $user->postAnalytics()->where('profile_id', '=', $profile->id)
      ->latest('updated_at')
      ->with('post.photo')->first();

    if ($postInteractions) {
    	$formattedPostInteractions = (object) [
    		'has_recent' => true,
    		'viewed_on' => $postInteractions->updated_at,
    		'is_redeemable' => $postInteractions->post->is_redeemable,
    		'is_event' => $postInteractions->post->event_date ? true : false,
    		'message' => $postInteractions->post->message,
    		'body' => $postInteractions->post->body,
    		'title' => $postInteractions->post->title,
    		'postImageUrl' => $postInteractions->post->photo->api_thumbnail_url
    	];
    } else {
    	$formattedPostInteractions = (object) [
    		'has_recent' => false
    	];
    }
    return $formattedPostInteractions;
  }

  public function getLoyaltyCard($user, $profile) {
    if ($loyaltyProgram = $profile->loyaltyProgram) {
      $loyaltyCard = $user->loyaltyCards()->where('loyalty_program_id', '=', $loyaltyProgram->id)->first();
      if ($loyaltyCard) {
        $formattedLoyaltyCard = (object) [
        	'has_reward' => $loyaltyCard->unredeemed_rewards > 0,
        	'loyalty_card_id' => $loyaltyCard->id,
        	'unredeemed_count' => $loyaltyCard->unredeemed_rewards,
        	'total_rewards_earned' => $loyaltyCard->rewards_achieved,
        	'loyalty_reward' => $loyaltyProgram->reward
        ];
      } else {
      	$formattedLoyaltyCard = (object) [
      		'has_reward' => false
      	];
      }
    } else {
      $formattedLoyaltyCard = (object) [
      	'has_reward' => false
      ];
    }
    return $formattedLoyaltyCard;
  }
}
