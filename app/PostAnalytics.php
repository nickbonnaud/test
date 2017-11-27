<?php

namespace App;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;

class PostAnalytics extends Model
{
	protected $fillable = [
  	'profile_id',
  	'post_id',
    'user_id',
  	'viewed',
  	'shared',
  	'bookmarked',
  ];

  protected $dates = ['viewed_on', 'shared_on', 'bookmarked_on'];

  public function user() {
    return $this->belongsTo('App\User');
  }

  public function profile() {
    return $this->belongsTo('App\Profile');
  }

  public function post() {
  	return $this->belongsTo('App\Post');
  }

  public function setViewedAttribute($viewed) {
    $this->attributes['viewed_on'] = Carbon::now(new DateTimeZone(config('app.timezone')));
    $this->attributes['viewed'] = $viewed;
  }

  public function setSharedAttribute($shared) {
    $this->attributes['shared_on'] = Carbon::now(new DateTimeZone(config('app.timezone')));
    if (!$this->viewed) {
      $this->attributes['viewed'] = true;
    }
    $this->attributes['shared'] = $shared;
  }

  public function setBookmarkedAttribute($bookmarked) {
    $this->attributes['bookmarked_on'] = Carbon::now(new DateTimeZone(config('app.timezone')));
    if (!$this->viewed) {
      $this->attributes['viewed'] = true;
    }
    $this->attributes['bookmarked'] = $bookmarked;
  }

  public function setTransactionResultedAttribute($transaction) {
    $this->attributes['transaction_on'] = Carbon::now(new DateTimeZone(config('app.timezone')));
    $this->attributes['transaction_resulted'] = $transaction;
  }

  public function scopeFilter($query, $filters, $profile, $type = null) {
    return $filters->apply($query, $type)->where('profile_id', '=', $profile->id);
  }



  public static function checkRecentlyViewed($user, $profile, $transaction) {
    $currentDate = Carbon::now();
    $fromDate = Carbon::now()->subDays(2);

    $viewedPostAnalytics = PostAnalytics::whereBetween('updated_at', [$fromDate, $currentDate])
      ->where('user_id', '=', $user->id)
      ->where('profile_id', '=', $profile->id)
      ->orderBy('viewed_on', 'desc')->first();

    if ($viewedPostAnalytics) {
      $viewedPostAnalytics->transaction_resulted = true;
      $viewedPostAnalytics->transaction_on = Carbon::now(new DateTimeZone(config('app.timezone')));
      $viewedPostAnalytics->total_revenue = $viewedPostAnalytics->total_revenue + $transaction->tips + $transaction->net_sales;

      $viewedPostAnalytics->save();
      self::setPostTotalRevenue($viewedPostAnalytics, $transaction);
    }
  }

  public static function setPostTotalRevenue($viewedPostAnalytics, $transaction) {
    $post = Post::findOrFail($viewedPostAnalytics->post_id);
    $post->total_revenue = $post->total_revenue + $transaction->tips + $transaction->net_sales;
    $post->save();
  }
}
