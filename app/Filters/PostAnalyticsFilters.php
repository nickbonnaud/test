<?php

namespace App\Filters;

class PostAnalyticsFilters extends Filters
{

  protected $filters = ['totalViews', 'totalPurchases', 'dayInteractions', 'dayRevenue', 'hourInteractions', 'hourRevenue'];

  protected function totalViews() {
    return $this->builder->where('viewed', '=', true);
  }

  protected function totalPurchases() {
    return $this->builder->where('transaction_resulted', '=', true);
  }

  protected function dayInteractions() {
    return $this->builder->selectRaw('WEEKDAY(updated_at) as date, COUNT(*) as count')
    ->groupBy('date')
    ->orderBy('date', 'ASC');
  }

  protected function dayRevenue() {
    return $this->builder->where('transaction_resulted', '=', true)
    ->selectRaw('WEEKDAY(transaction_on) as date, sum(total_revenue) as total_revenue')
    ->groupBy('date')
    ->orderBy('date', 'ASC');
  }

  protected function hourInteractions() {
    return $this->builder->selectRaw('HOUR(updated_at) as hour, COUNT(*) as count')
    ->groupBy('hour')
    ->orderBy('hour', 'ASC');
  }

  protected function hourRevenue() {
    return $this->builder->where('transaction_resulted', '=', true)
    ->selectRaw('HOUR(transaction_on) as hour, sum(total_revenue) as total_revenue')
    ->groupBy('hour')
    ->orderBy('hour', 'ASC');
  }
}
