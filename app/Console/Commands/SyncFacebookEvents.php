<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Profile;
use App\Post;

class SyncFacebookEvents extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:sync-facebook-events';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Add Events to profiles';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
      parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $profiles = Profile::whereNotNull('fb_page_id')->whereNotNull('fb_app_id')->get();
    foreach ($profiles as $profile) {
      $facebookEvents = $profile->getFacebookEvents();
      dd($facebookEvents);
      self::createEvents($facebookEvents, $profile);
    }
  }

  public static function createEvents($facebookEvents, $profile) {
    foreach ($facebookEvents as $facebookEvent) {
      if (! Post::where('fb_post_id', '=', $facebookEvent->id)->first()) {
        Post::createEvent($facebookEvent, $profile);
      }
    }
  }
}
