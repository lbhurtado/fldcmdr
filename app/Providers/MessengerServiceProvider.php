<?php

namespace App\Providers;

use BotMan\BotMan\BotMan;
use App\Channels\MessengerChannel;
use Illuminate\Support\ServiceProvider;

class MessengerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->when(MessengerChannel::class)
            ->needs(BotMan::class)
            ->give(function () {
                $botman = resolve('botman');

                return $botman;
            });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
