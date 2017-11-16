<?php

namespace App\Filters;

use Carbon\Carbon;

class UserFilters extends Filters
{

  protected $filters = ['defaultDate', 'customDate', 'email', 'first', 'last', 'onShift'];

  protected function first($firstName) {
    return $this->builder->where('first_name', '=', $firstName);
  }

  protected function last($lastName) {
    return $this->builder->where('last_name', '=', $lastName);
  }

  protected function email($email) {
    return $this->builder->where('email', '=', $email);
  }

  protected function defaultDate() {
  	$currentDate = Carbon::now();
  	$fromDate = $this->getFromDate($currentDate);
    return $this->builder->leftJoin('transactions', 'users.id', '=', 'transactions.employee_id')
    	->whereBetween('transactions.updated_at', [$fromDate, $currentDate])
    	->where('refund_full', '=', false)
    	->select('users.id', 'first_name', 'last_name', 'photo_path', 'role', 'employer_id', 'on_shift')
      ->groupBy('users.id');
  }

  protected function customDate($dateRange) {
    return $this->builder->leftJoin('transactions', 'users.id', '=', 'transactions.employee_id')
    	->whereBetween('transactions.updated_at', [$dateRange[0], $dateRange[1]])
    	->where('refund_full', '=', false)
    	->select('users.id', 'first_name', 'last_name', 'photo_path', 'role', 'employer_id', 'on_shift')
      ->groupBy('users.id');
  }

  protected function onShift() {
    return $this->builder->where('on_shift', '=', true);
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
