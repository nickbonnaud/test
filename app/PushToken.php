<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushToken extends Model {

  public function user() {
    return $this->belongsTo('App\User');
  }
}