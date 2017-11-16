<?php

namespace App\Filters;
use Illuminate\Http\Request;

abstract class Filters
{
 
  protected $request;
  protected $builder;
  protected $type;
  protected $filters = [];


  public function __construct(Request $request)
  {
    $this->request = $request;
  }

  public function apply($builder, $type = null) {
    $this->builder = $builder;
    $this->type = $type;

    foreach ($this->getFilters() as $filter => $value) {
      if (method_exists($this, $filter)) {
        $this->$filter($value);
      }
    }

    return $this->builder;
  }

  protected function getFilters() {
    if ($this->type) {
      return [$this->type => 0];
    } else {
      return $this->request->only($this->filters);
    }
  }
}
