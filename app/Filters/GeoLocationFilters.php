<?php

namespace App\Filters;

use Carbon\Carbon;

class GeoLocationFilters extends Filters
{

  protected $filters = ['city'];

  protected function city($city) {
    return $this->builder->whereHas('profile.city', function($query) use($city) {
      $query->where('slug', '=', $city);
    });
  }
}
