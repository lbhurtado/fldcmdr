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

        switch (true)
        {
            case $sms->match("#{tag}", function ($tag) use ($sms) {
                \Log::info('tag = ' . $tag);
                Command::tag($sms->from, ['keyword' => $tag]);
                return true;
            }): break;

            case $sms->match("&{group}", function ($group) use ($sms) {
                \Log::info('group = ' . $group);
                return true;
            }): break;

            case $sms->match("@{area}", function ($area) use ($sms) {
                \Log::info('area = ' . $area);
                return true;
            }): break;

            case $sms->match("?{status}", function ($status) use ($sms) {
                \Log::info('status = ' . $status);
                return true;
            }): break;

            case $sms->match("!{warning}", function ($warning) use ($sms) {
                \Log::info('warning = ' . $warning);
                return true;
            }): break;

            default:
                $sms->match("{*.}", function ($catch) use ($sms) {
                    \Log::info('catch = ' . $catch);
                    Command::claim($sms->from, ['keyword' => $tag]);
                }); 
        }
    }

    public function subscribe($events)
    {
        $events->listen(
            SMSEvents::CREATED, 
            SMSEventSubscriber::class.'@onSMSCreated'
        );
    }  
}
