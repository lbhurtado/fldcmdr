<?php

namespace App\Listeners;

use App\User;
use Spatie\Permission\Models\Role;
use App\Events\{UserEvent, UserEvents};
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class UserEventSubscriber
{
    public function onUserCreated(UserEvent $event)
    {

    }

    public function onUserVerified(UserEvent $event)
    {
        if (config('chatbot.verify.reward.enabled'))
            $event->getUser()->sendAirTime('verified');

        if (config('chatbot.verify.notify.parent', true))
            optional($event->getUser()->parent, function(User $parent) use ($event) {
                $parent->notifyVerificationOfDownline($event->getUser());
            });
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
