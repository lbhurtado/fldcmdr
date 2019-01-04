<?php

namespace App\Providers;

use App\Services\EngageSpark;
use App\Channels\EngageSparkChannel;
use Illuminate\Support\ServiceProvider;

class EngageSparkServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(EngageSpark::class, function ($app) {
            return new EngageSpark($app['config']['services.engagespark']);
        });
    }
}