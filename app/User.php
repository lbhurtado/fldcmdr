<?php

namespace App;

use App\Eloquent\{Phone, Messenger};
use Illuminate\Notifications\Notifiable;
use App\Traits\{HasNotifications, Verifiable};
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasNotifications, Verifiable;

    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'driver', 'channel_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }
    
    public function scopeWithMobile($query, $value)
    {
        return $query->where('mobile', Phone::number($value));
    }
}
