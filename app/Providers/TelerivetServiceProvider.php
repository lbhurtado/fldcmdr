<?php

namespace App\Providers;

use App\Services\Telerivet;
use App\Channels\TelerivetChannel;
use Illuminate\Support\ServiceProvider;

class TelerivetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->when(TelerivetChannel::class)
            ->needs(Telerivet::class)
            ->give(function () {
                $config = config('broadcasting.connections.telerivet');

                return new Telerivet($config['api_key'], $config['project_id'], $config['service_id']);
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
