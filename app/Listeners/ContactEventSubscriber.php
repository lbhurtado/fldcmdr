<?php

namespace App\Listeners;

use App\Contact;
use Spatie\Permission\Models\Role;
use App\Events\{ContactEvent, ContactEvents};
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class ContactEventSubscriber
{
    public function onContactCreated(ContactEvent $event)
    {

    }

    public function subscribe($events)
    {
        $events->listen(
            ContactEvents::CREATED, 
            ContactEventSubscriber::class.'@onContactCreated'
        );
    }  
}
