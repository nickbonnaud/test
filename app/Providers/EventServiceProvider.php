<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CustomerBreakGeoFence' => [
            'App\Listeners\AddRemoveUser',
            'App\Listeners\AddRemoveUserPockeytLite',
        ],
        'App\Events\CustomerEarnReward' => [
            'App\Listeners\NotifyReward',
        ],
        'App\Events\TransactionError' => [
            'App\Listeners\NotifyError',
        ],
        'App\Events\CustomerRequestBill' => [
            'App\Listeners\ShowBillRequest',
        ],
        'App\Events\TransactionSuccess' => [
            'App\Listeners\NotifySuccess',
        ],
        'App\Events\TransactionsChange' => [
            'App\Listeners\UpdateTransactions',
        ],
        'Illuminate\Notifications\Events\NotificationSent' => [
            'App\Listeners\SendNotification',
        ],
        'App\Events\BillPushSuccess' => [
            'App\Listeners\NotifyBillPushSuccess',
        ],
        'App\Events\Debug' => [
            'App\Listeners\FireDebug',
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\Instagram\InstagramExtendSocialite@handle'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
