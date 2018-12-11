<?php

namespace App\Listeners;

use App\Events\{UserEvent, UserEvents};
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Permission\Models\Role;

class UserEventSubscriber
{
    public function onUserCreated($event)
    {

    }

    public function onUserVerified($event)
    {

    }

    public function subscribe($events)
    {
        $events->listen(
            UserEvents::CREATED, 
            UserEventSubscriber::class.'@onUserCreated'
        );

        $events->listen(
            UserEvents::VERIFIED, 
            UserEventSubscriber::class.'@onUserVerified'
        );
    }  
}
