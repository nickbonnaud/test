<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoyaltyProgram extends Model
{
	protected $fillable = ['profile_id', 'is_increment', 'purchases_required', 'amount_required', 'reward'];

	protected static function boot() {
    parent::boot();
    static::deleting(function ($loyaltyProgram) {
      $loyaltyProgram->loyaltyCards->each->delete();
    });
  }

  public function profile() {
    return $this->belongsTo('App\Profile');
  }

  public function loyaltyCards() {
    return $this->hasMany('App\LoyaltyCard');
  }

  public function setAmountRequiredAttribute($amountRequired) {
    $this->attributes['amount_required'] =  preg_replace("/[^0-9\.]/","", $amountRequired) * 100;
    $this->attributes['is_increment'] =  false;
  }

  public function setPurchasesRequiredAttribute($purchasesRequired) {
    $this->attributes['purchases_required'] =  $purchasesRequired;
    $this->attributes['is_increment'] =  true;
  }

  public function getAmountRequiredAttribute($amountRequired) {
    return round($amountRequired / 100, 2);
  }

  public function setRewardAttribute($reward) {
    $this->attributes['reward'] =  lcfirst($reward);
  }
}
