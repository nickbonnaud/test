<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beacon extends Model {

	protected $fillable = [
  	'uuid',
  	'identifier'
  ];

  public function profile() {
    return $this->belongsTo(Profile::class);
  }
}
