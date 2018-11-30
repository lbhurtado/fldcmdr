<?php

namespace App\Traits;

use App\Jobs\RegisterTelerivetService;

trait HasNotifications
{
    public function routeNotificationForTelerivet()
    {
        return $this->telerivet_id;
    }

    public function registerTelerivet()
    {
        RegisterTelerivetService::dispatch($this);

        return $this;
    }

    public function routeNotificationForMessenger()
    {
    	$driver = $this->driver;
    	$channel_id = $this->channel_id;

    	return compact('driver', 'channel_id');
    }
}
