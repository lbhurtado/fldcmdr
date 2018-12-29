<?php

namespace App\Providers;

use App\{User, SMS, Contact};
use Illuminate\Support\ServiceProvider;
use App\Observers\{UserObserver, SMSObserver, ContactObserver};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        SMS::observe(SMSObserver::class);
        User::observe(UserObserver::class);
        Contact::observe(ContactObserver::class);
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
