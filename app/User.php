<?php

namespace App;

use App\Helpers\Phone;
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

    public static function fromMessenger($driver, $channel_id)
    {
        return static::where(compact('driver', 'channel_id'))->firstOrFail();
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }
    
    public function scopeWithMobile($query, $value)
    {
        return $query->where('mobile', Phone::number($value));
    }

    public function scopeVerified($query)
    {
        return $query->whereDate('verified_at', '=', now()->toDateString());
    }
}
