<?php

namespace App\Observers;

use App\User;
use App\Events\{UserEvent, UserEvents};

class UserObserver
{
    public function created(User $user)
    {
        event(UserEvents::CREATED, new UserEvent($user));
    }
}
