<?php

namespace App\Filters;


class ProfileFilters extends Filters
{

  protected $filters = ['city'];

  protected function city($city) {
    return $this->builder->whereHas('city', function($query) use($city) {
      $query->where('slug', '=', $city);
    })->orderBy('business_name', 'ASC');
  }

  protected function query($search) {
  	return $this->builder->where('business_name', 'like', '%' . $search . '%')
  		->orderBy('business_name', 'ASC');
  }
}
