<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class LoyaltyCardResource extends Resource
{
  public function toArray($request)
  {

    return [
      'id' => $this->id,
      'current_amount' => $this->current_amount,
      'rewards_achieved' => $this->rewards_achieved,
      'is_increment' => $this->loyaltyProgram->is_increment,
      'purchases_required' => $this->loyaltyProgram->purchases_required,
      'amount_required' => $this->loyaltyProgram->amount_required,
      'reward' => $this->loyaltyProgram->reward,
      'business_name' => $this->loyaltyProgram->profile->business_name,
      'logo' => $this->loyaltyProgram->profile->logo->apiThumbnailUrl,
      'last_purchase_date' => $this->updated_at
    ];
  }
}
