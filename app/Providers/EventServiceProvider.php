<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use App\Listeners\SMSEventSubscriber;
use App\Listeners\UserEventSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $subscribe = [
        SMSEventSubscriber::class,
        UserEventSubscriber::class,
    ];

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // 'App\Events\Event' => [
        //     'App\Listeners\EventListener',
        // ],
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
