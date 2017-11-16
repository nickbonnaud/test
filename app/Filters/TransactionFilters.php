<?php

namespace App\Filters;

use Carbon\Carbon;

class TransactionFilters extends Filters
{

  protected $filters = ['defaultDate', 'customDate', 'pending', 'finalized', 'recent', 'deals'];

  protected function defaultDate() {
  	$currentDate = Carbon::now();
  	$fromDate = $this->getFromDate($currentDate);
    return $this->builder->whereBetween('updated_at', [$fromDate, $currentDate])->where('refund_full', '=', false);
  }

  protected function customDate($dateRange) {
    return $this->builder->whereBetween('updated_at', [$dateRange[0], $dateRange[1]])->where('refund_full', '=', false);
  }

  protected function pending() {
    return $this->builder->where('status', '<', '20')
      ->where('refund_full', '=', false)
      ->orderBy('status', 'asc');
  }

  protected function finalized() {
    return $this->builder->where('status', '=', '20')
      ->where('refund_full', '=', false)
      ->orderBy('updated_at', 'desc')
      ->take(10);
  }

  protected function recent() {
    return $this->builder->where('status', '=', '20')
      ->where('paid', '=', true)
      ->whereNull('deal_id')
      ->where('refund_full', '=', false)
      ->orderBy('created_at', 'desc');
  }

  protected function deals() {
   return $this->builder->with('deal')->where('status', '=', '20')
    ->where('paid', '=', true)
    ->whereNotNull('deal_id')
    ->where('refund_full', '=', false)
    ->orderBy('created_at', 'desc');
  }


  private function getFromDate($currentDate) {
  	$fromDate = Carbon::now();
  	$fromDate->hour = 4;
  	if ($currentDate <= $fromDate) {
    	$fromDate = Carbon::now()->subDay()->hour = 4;
    }
    return $fromDate;
  }
}
