<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * The Artisan commands provided by your application.
   *
   * @var array
   */
  protected $commands = [
    Commands\SyncFacebookEvents::class,
    Commands\SyncTransactionsQuickbooks::class
  ];

  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
    $schedule->command('app:sync-facebook-events')->dailyAt('23:30');
    $schedule->command('app:sync-transactions-quickbooks')->dailyAt('03:00');
  }

  /**
   * Register the commands for the application.
   *
   * @return void
   */
  protected function commands()
  {
      $this->load(__DIR__.'/Commands');

      require base_path('routes/console.php');
  }
}
