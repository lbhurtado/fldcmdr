<?php

namespace App\Observers;

use App\User;
use Spatie\Permission\Models\Role;

class UserObserver
{
    public function creating(User $user)
    {
        $user->password = $user->password ?? bcrypt(env('DEFAULT_PASSWORD', '1234'));
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
