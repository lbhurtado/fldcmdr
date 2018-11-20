<?php

namespace App;

use App\Eloquent\Phone;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\{HasNotifications, Verifiable, HasSchemalessAttributes};

class User extends Authenticatable
{
    use Notifiable;

    use HasNotifications, Verifiable, HasSchemalessAttributes, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'driver', 'channel_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public $casts = [
        'extra_attributes' => 'array',
    ];

    protected $guard_name = 'web';

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }
    
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function scopeWithMobile($query, $value)
    {
        return $query->where('mobile', Phone::number($value));
    }

    public function checkin(...$coordinates)
    {
        $coordinates = array_flatten($coordinates);
        $longitude = $coordinates[0];
        $latitude = $coordinates[1];

        $checkin = $this->checkins()->create(compact('longitude', 'latitude'));

        return $checkin;
    }
}
