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

            case $sms->match("{campaign}#{keyword}", function ($campaign, $keyword) use ($sms) {
                \Log::info(compact('campaign', 'keyword'));
                Command::tag($sms->from, compact('campaign', 'keyword'));
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

            case $sms->match("broadcast {message}", function ($message) use ($sms) {
                \Log::info(['message' => $message]);
                Command::broadcast($sms->from, ['message' => $message]);
                return true;
            }); break;

            case $sms->match("Broadcast {message}", function ($message) use ($sms) {
                \Log::info(['message' => $message]);
                Command::broadcast($sms->from, ['message' => $message]);
                return true;
            }); break;

            case $sms->match("pick {count} {campaign}", function ($count, $campaign) use ($sms) {
                \Log::info(['count' => $count, 'campaign' => $campaign]);
                Command::pick($sms->from, ['count' => $count, 'campaign' => $campaign]);
                return true;
            }); break;

            case $sms->match("Pick {count} {campaign}", function ($count, $campaign) use ($sms) {
                \Log::info(['count' => $count, 'campaign' => $campaign]);
                Command::pick($sms->from, ['count' => $count, 'campaign' => $campaign]);
                return true;
            }); break;

            default:
                $sms->match("{*.}", function ($catch) use ($sms) {
                    \Log::info('catch = ' . $catch);
                    $args = explode(' ', $catch);
                    $keyword = array_shift($args);
                    $name = count($args) ? implode(' ', $args) : null;
                    Command::claim($sms->from, compact('keyword', 'name'));
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
