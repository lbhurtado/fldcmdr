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
}
