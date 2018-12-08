<?php

namespace App\Providers;

use App\{User, SMS};
use Illuminate\Support\ServiceProvider;
use App\Observers\{UserObserver, SMSObserver};

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
