<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The policy mappings for the application.
   *
   * @var array
   */
  protected $policies = [
    'App\Profile' => 'App\Policies\ProfilePolicy',
    'App\GeoLocation' => 'App\Policies\GeoLocationPolicy',
    'App\Account' => 'App\Policies\AccountPolicy',
    'App\Post' => 'App\Policies\PostPolicy',
    'App\Product' => 'App\Policies\ProductPolicy',
    'App\Transaction' => 'App\Policies\TransactionPolicy',
    'App\User' => 'App\Policies\UserPolicy'
  ];

  /**
   * Register any authentication / authorization services.
   *
   * @return void
   */
  public function boot()
  {
    $this->registerPolicies();
  }
}
