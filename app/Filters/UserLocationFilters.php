<?php

namespace App\Filters;

use Carbon\Carbon;

class UserLocationFilters extends Filters
{

  protected $filters = ['default'];

  protected function default() {
  	$currentTime = Carbon::now();
  	$fromTime = $this->getFromTime();
    return $this->builder->whereBetween('updated_at', [$fromTime, $currentTime]);
  }

  private function getFromTime() {
  	return Carbon::now()->subMinutes(10); 
  }
}
