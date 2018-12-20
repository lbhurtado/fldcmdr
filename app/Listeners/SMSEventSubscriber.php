<?php

namespace App\Listeners;

use App\{User, Stub};
use App\Events\{SMSEvent, SMSEvents};
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SMSEventSubscriber
{
    public function onSMSCreated(SMSEvent $event)
    {
        $event->getSMS()->checkStubAndInvite();
    }

    public function subscribe($events)
    {
        $events->listen(
            SMSEvents::CREATED, 
            SMSEventSubscriber::class.'@onSMSCreated'
        );
    }  
}
