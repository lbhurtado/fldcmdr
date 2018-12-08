<?php

namespace App\Listeners;

use \Log;
use App\Events\{SMSEvent, SMSEvents};
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SMSEventSubscriber
{
    public function onSMSCreating(SMSEvent $event)
    {

    }

    public function onSMSCreated(SMSEvent $event)
    {
        Log::info($event->getSMS()->toArray());
    }

    public function subscribe($events)
    {
        $events->listen(
            SMSEvents::CREATING, 
            SMSEventSubscriber::class.'@onSMSCreating'
        );

        $events->listen(
            SMSEvents::CREATED, 
            SMSEventSubscriber::class.'@onSMSCreated'
        );
    }  
}
