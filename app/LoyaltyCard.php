<?php

namespace App;

use App\LoyaltyCard;
use App\Events\CustomerEarnReward;
use App\Notifications\LoyaltyRewardEarned;
use App\Notifications\CustomerRedeemReward;
use Illuminate\Database\Eloquent\Model;

class LoyaltyCard extends Model
{
	protected $fillable = [
  	'user_id',
  	'loyalty_program_id',
  	'current_amount',
  	'rewards_achieved',
    'unredeemed_rewards'
  ];

  public function user() {
    return $this->belongsTo('App\User');
  }

  public function loyaltyProgram() {
    return $this->belongsTo('App\loyaltyProgram');
  }

  public static function updateWithTransactions($user, $profile, $transaction) {
    if ($loyaltyProgram = $profile->loyaltyProgram) {
      self::getUserLoyaltyCard($user, $loyaltyProgram, $transaction);
    }
  }

  public static function getUserLoyaltyCard($user, $loyaltyProgram, $transaction) {
    $loyaltyCard = LoyaltyCard::where('user_id', '=', $user->id)
      ->where('loyalty_program_id', '=', $loyaltyProgram->id)
      ->first();
    if (!isset($loyaltyCard)) {
      $loyaltyCard = self::createLoyaltyCard($user, $loyaltyProgram);
    }
    self::addToLoyaltyCard($loyaltyCard, $loyaltyProgram, $transaction);
  }

  public static function createLoyaltyCard($user, $loyaltyProgram) {
    $loyaltyCard = new LoyaltyCard(['loyalty_program_id' => $loyaltyProgram->id]);
    $user->loyaltyCards()->save($loyaltyCard);
    return $loyaltyCard;
  }

  public static function addToLoyaltyCard($loyaltyCard, $loyaltyProgram, $transaction) {
    if ($loyaltyProgram->is_increment) {
      self::addIncrementAmount($loyaltyCard, $loyaltyProgram);
    } else {
      self::addMonetaryAmount($loyaltyCard, $loyaltyProgram, $transaction);
    }
  }

  public static function addIncrementAmount($loyaltyCard, $loyaltyProgram) {
    if ($loyaltyCard->current_amount + 1 == $loyaltyProgram->purchases_required) {
      $loyaltyCard->current_amount = 0;
      $loyaltyCard->rewards_achieved = $loyaltyCard->rewards_achieved + 1;
      $loyaltyCard->unredeemed_rewards = $loyaltyCard->unredeemed_rewards + 1;
      $loyaltyCard->save();
      $rewardsQuantity = 1;
      self::customerRewardEvent($loyaltyCard->user, $loyaltyProgram, $rewardsQuantity);
    } else {
      $loyaltyCard->current_amount = $loyaltyCard->current_amount + 1;
      $loyaltyCard->save();
    }
  }

  public static function addMonetaryAmount($loyaltyCard, $loyaltyProgram, $transaction) {
    if ($loyaltyCard->current_amount + $transaction->total >= $loyaltyProgram->amount_required) {
      $previousRewardsAchieved = $loyaltyCard->rewards_achieved;
      $loyaltyCard->current_amount = $loyaltyCard->current_amount + $transaction->total;
      
      while($loyaltyCard->current_amount >= $loyaltyProgram->amount_required) {
        $loyaltyCard->rewards_achieved = $loyaltyCard->rewards_achieved + 1;
        $loyaltyCard->current_amount = $loyaltyCard->current_amount - $loyaltyProgram->amount_required;
      }
      $rewardsQuantity = $loyaltyCard->rewards_achieved - $previousRewardsAchieved;
      $loyaltyCard->unredeemed_rewards = $loyaltyCard->unredeemed_rewards + $rewardsQuantity;
      $loyaltyCard->save();
      self::customerRewardEvent($loyaltyCard->user, $loyaltyProgram, $rewardsQuantity);
    } else {
      $loyaltyCard->current_amount = $loyaltyCard->current_amount + $transaction->total;
      $loyaltyCard->save();
    }
  }

  public static function customerRewardEvent($user, $loyaltyProgram, $rewardsQuantity) {
    event(new CustomerEarnReward($user, $loyaltyProgram, $rewardsQuantity));
    $user->notify(new LoyaltyRewardEarned($loyaltyProgram, $rewardsQuantity));
  }

  public function sendRedeemRequestToCustomer() {
    $this->user->notify(new CustomerRedeemReward($this, $this->loyaltyProgram));
  }

  public function subtractUnredeemedRewards() {
    $this->unredeemed_rewards = $this->unredeemed_rewards - 1;
    $this->save();
  }
}
