<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot() {
    \View::composer('*', function ($view) {
      $user = auth()->user();
      if ($user) {
        $profile = $user->profile;
        $view->with('user', $user)->with('profile', $profile);
      } else {
        $view->with('user', $user);
      }
    });
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
      //
  }
}
