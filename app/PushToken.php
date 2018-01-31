<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushToken extends Model {

  protected $fillable = [
  	'push_token',
  	'device'
  ];

  public function user() {
    return $this->belongsTo('App\User');
  }
}