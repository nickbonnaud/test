<?php

namespace App\Filters;


class ProfileFilters extends Filters
{

  protected $filters = ['city', 'query', 'rating', 'tags'];

  protected function city($city) {
    return $this->builder->whereHas('city', function($query) use($city) {
      $query->where('slug', '=', $city);
    });
  }

  protected function query($search) {
  	return $this->builder->where('business_name', 'LIKE', "%$search%");
  }

  protected function rating() {
  	return $this->builder->orderBy('google_rating', 'DESC');
  }

  protected function tags($ids) {
  	return $this->builder->whereHas('tags', function($query) use ($ids) {
  		$query->whereIn('id', $ids);
  	});
  }

  protected function id($id) {
    return $this->builder->where('id', '=', $id);
  }
}
