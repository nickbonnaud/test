<?php

namespace App\Filters;

use Carbon\Carbon;

class TransactionFilters extends Filters
{

  protected $filters = ['defaultDate', 'customDate', 'pending', 'finalized', 'recent', 'redeemedDeals', 'unRedeemedDeals', 'customerPending', 'dealsAll', 'clover'];

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

  protected function customerPending() {
    return $this->builder->where('status', '<', '20')
      ->where('paid', false)
      ->whereNull('deal_id')
      ->where('is_refund', false)
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

  protected function redeemedDeals() {
    return $this->builder->with('deal')->where('status', '=', '20')
      ->where('paid', '=', true)
      ->whereNotNull('deal_id')
      ->where('refund_full', '=', false)
      ->where('redeemed', '=', true)
      ->orderBy('created_at', 'desc');
  }

  protected function unRedeemedDeals() {
    return $this->builder->with('deal')->where('status', '=', '20')
      ->where('paid', '=', true)
      ->whereNotNull('deal_id')
      ->where('refund_full', '=', false)
      ->where('redeemed', '=', false)
      ->orderBy('created_at', 'desc');
  }

  protected function dealsAll() {
    return $this->builder->where('status', '=', '20')
    ->where('paid', '=', true)
    ->whereNotNull('deal_id')
    ->where('refund_full', '=', false)
    ->orderBy('created_at', 'desc')
    ->select('deal_id', 'redeemed', 'created_at');
  }

  protected function clover($cloverTransactionId) {
    return $this->builder->where('pos_transaction_id', $cloverTransactionId)
      ->where('paid', false)
      ->whereNull('deal_id')
      ->where('is_refund', false);
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
