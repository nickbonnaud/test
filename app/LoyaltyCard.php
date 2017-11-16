<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoyaltyCard extends Model
{
	protected $fillable = [
  	'user_id',
  	'loyalty_program_id',
  	'current_amount',
  	'rewards_achieved'
  ];

  public function user() {
    return $this->belongsTo('App\User');
  }

  public function loyaltyProgram() {
    return $this->belongsTo('App\loyaltyProgram');
  }
}
