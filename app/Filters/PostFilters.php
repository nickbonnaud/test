<?php

namespace App\Filters;

use Carbon\Carbon;

class PostFilters extends Filters
{

  protected $filters = ['profilePosts', 'profileEvents', 'profileDeals', 'interactionsWeek', 'interactionsMonth', 'interactionsTwoMonth', 'revenueWeek', 'revenueMonth', 'revenueTwoMonth', 'city', 'favs', 'event', 'bookmarks', 'business', 'explore'];

  protected function profilePosts() {
    return $this->builder->whereNull('event_date')->where('is_redeemable', '=', false)->orderBy('published_at', 'desc')->limit(10);
  }

  protected function profileEvents() {
    return $this->builder->whereNotNull('event_date')->orderBy('published_at', 'desc')->limit(10);
  }

  protected function profileDeals() {
    return $this->builder->where('is_redeemable', '=', true)->orderBy('published_at', 'desc')->limit(10)->get();
  }

  protected function interactionsWeek() {
    $currentDate = Carbon::now();
    $fromDate = Carbon::now()->subWeek();

    return $this->builder->whereBetween('updated_at', [$fromDate, $currentDate])
    ->orderBy('total_interactions', 'desc')
    ->where('total_interactions', '>', 0)
    ->take(10);
  }

  protected function interactionsMonth() {
    $currentDate = Carbon::now();
    $fromDate = Carbon::now()->subMonth();

    return $this->builder->whereBetween('updated_at', [$fromDate, $currentDate])
    ->orderBy('total_interactions', 'desc')
    ->where('total_interactions', '>', 0)
    ->take(10);
  }

  protected function interactionsTwoMonth() {
    $currentDate = Carbon::now();
    $fromDate = Carbon::now()->subMonths(2);

    return $this->builder->whereBetween('updated_at', [$fromDate, $currentDate])
    ->orderBy('total_interactions', 'desc')
    ->where('total_interactions', '>', 0)
    ->take(10);
  }

  protected function revenueWeek() {
    $currentDate = Carbon::now();
    $fromDate = Carbon::now()->subWeek();

    return $this->builder->whereBetween('updated_at', [$fromDate, $currentDate])
    ->orderBy('total_revenue', 'desc')
    ->where('total_revenue', '>', 0)
    ->take(10);
  }

  protected function revenueMonth() {
    $currentDate = Carbon::now();
    $fromDate = Carbon::now()->subMonth();

    return $this->builder->whereBetween('updated_at', [$fromDate, $currentDate])
    ->orderBy('total_revenue', 'desc')
    ->where('total_revenue', '>', 0)
    ->take(10);
  }

  protected function revenueTwoMonth() {
    $currentDate = Carbon::now();
    $fromDate = Carbon::now()->subMonths(2);

    return $this->builder->whereBetween('updated_at', [$fromDate, $currentDate])
    ->orderBy('total_revenue', 'desc')
    ->where('total_revenue', '>', 0)
    ->take(10);
  }

  protected function city($city) {
    return $this->builder->whereHas('profile.city', function($query) use($city) {
      $query->where('slug', '=', $city);
    });
  }

  protected function explore() {
    return $this->builder->where('is_redeemable', '=', false)
      ->orWhere('end_date', '>', Carbon::now())
      ->latest();
  }

  protected function favs($ids) {
    return $this->builder->whereIn('profile_id', $ids)
      ->where(function ($query) {
        $query->where('is_redeemable', '=', false)
              ->orWhere('end_date', '>', Carbon::now());
      })->latest();
  }

  protected function business($id) {
    return $this->builder->where('profile_id', $id)
      ->where(function ($query) {
        $query->where('is_redeemable', '=', false)
              ->orWhere('end_date', '>', Carbon::now());
      })->latest();
  }

  protected function event($range) {
    $formattedRange = $this->formatDateRangeQuery($range);
    return $this->builder->whereBetween('event_date', $formattedRange)->orderBy('event_date');
  }

  protected function bookmarks($bookmarkIds) {
    return $this->builder->whereIn('id', $bookmarkIds)->latest();
  }




  public function formatDateRangeQuery($range) {
    switch($range) {
      case 'today':
        return [Carbon::today(), (Carbon::tomorrow())->subMinute()];
      case 'tomorrow':
        return [Carbon::tomorrow(), (Carbon::tomorrow())->addDay()->subMinute()];
      case 'week':
        return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
      case 'weekend':
        return [(Carbon::now()->startOfWeek())->addDays(4), Carbon::now()->endOfWeek()];
    }
  }
}
