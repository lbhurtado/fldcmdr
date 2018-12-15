<?php

namespace App\Listeners;

use App\Events\{UserEvent, UserEvents};
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Permission\Models\Role;

class UserEventSubscriber
{
    public function onUserCreated(UserEvent $event)
    {

    }

    public function onUserVerified(UserEvent $event)
    {
        if (config('chatbot.verify.reward.enabled')) {
            $amount = config('chatbot.verify.reward.amount');
            if ($amount > 0) {
                $event->getUser()->sendAirTime('verified');
                // $event->getUser()->sendReward($amount);
            }
        }
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
