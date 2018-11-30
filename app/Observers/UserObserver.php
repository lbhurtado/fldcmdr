<?php

namespace App\Observers;

use App\User;
use Spatie\Permission\Models\Role;

class UserObserver
{
    public function creating(User $user)
    {
        $user->name = $user->name ?? $user->driver . "." . $user->channel_id;
        $user->email = $user->name . '@' . env('DEFAULT_DOMAIN_NAME', 'serbis.io');
        $user->password = $user->password ?? bcrypt(env('DEFAULT_PASSWORD', '1234'));
        $user->extra_attributes['handle'] = str_slug($user->name, '.'); 
        $user->extra_attributes['wants_notifications'] = false;
    }

    public function created(User $user)
    {

    }

    public function updating(User $user)
    {

    }

    public function updated(User $user)
    {
       
    }

    public function deleted(User $user)
    {

    }

    public function restored(User $user)
    {

    }

    public function forceDeleted(User $user)
    {

    }
}
