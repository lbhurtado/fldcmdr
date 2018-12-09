<?php

namespace App\Listeners;

use App\{User, Stub};
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
        optional(Stub::validate($event->getSMS()->content), function ($stub) use ($event) {
            $stub->user
                ->invitees()
                ->updateOrCreate([
                    'mobile' => $event->getSMS()->from_number
                ],[
                    'role' => $stub->role,
                    'message' => trans('invite.message'),
                ]);
        });
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
