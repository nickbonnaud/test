<?php

namespace App\Filters;

class ProductFilters extends Filters
{

  protected $filters = ['categories'];

  protected function categories() {
    return $this->builder->whereNotNull('category')->select('category')->distinct();
  }
}
