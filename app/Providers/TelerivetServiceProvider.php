<?php

namespace App\Providers;

use App\Services\Telerivet;
use App\Channels\TelerivetChannel;
use Illuminate\Support\ServiceProvider;

class TelerivetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app
            ->when (TelerivetChannel::class)
            ->needs(Telerivet::class)
            ->give (function () {
                return new Telerivet();
            });
    }

    public function register()
    {
        //
    }
}
