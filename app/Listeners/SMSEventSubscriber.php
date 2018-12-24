<?php

namespace App\Listeners;

use App\{User, Stub, Command};
use App\Events\{SMSEvent, SMSEvents};
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SMSEventSubscriber
{
    public function onSMSCreated(SMSEvent $event)
    {
        $sms = $event->getSMS();
        $sms->checkStubAndInvite();

        $sms->match('#{tag}', function ($tag) {
            \Log::info('tag = ' . $tag);
            Command::tag($tag);
        });

        $sms->match('?{status}', function ($status) {
            \Log::info('status = ' . $status);
        });
    }

    public function subscribe($events)
    {
        $events->listen(
            SMSEvents::CREATED, 
            SMSEventSubscriber::class.'@onSMSCreated'
        );
    }  
}
