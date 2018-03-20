<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beacon extends Model
{
  protected static function boot() {
    parent::boot();
    static::saving(function ($beacon) {
      $beacon->identifier = $beacon->profile->slug;
    });
  }

  public function profile() {
    return $this->belongsTo(Profile::class);
  }
}
