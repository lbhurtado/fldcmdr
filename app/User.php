<?php

namespace App;

use App\Invitee;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\{HasNotifications, Verifiable, HasSchemalessAttributes, HasMobile, Askable};

class User extends Authenticatable
{
    use Notifiable;

    use HasNotifications, Verifiable, HasSchemalessAttributes, HasRoles, NodeTrait, HasMobile, Askable;

    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'driver', 'channel_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public $casts = [
        'extra_attributes' => 'array',
        'status' => 'array',
    ];

    protected $guard_name = 'web';

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }
    
    public function invitees()
    {
        return $this->hasMany(Invitee::class);
    }

    public function checkin(...$coordinates)
    {
        $coordinates = array_flatten($coordinates);
        $longitude = $coordinates[0];
        $latitude = $coordinates[1];

        $checkin = $this->checkins()->create(compact('longitude', 'latitude'));

        return $checkin;
    }

    public function hydrateFromInvitee()
    {
        optional(Invitee::withMobile($this->mobile)->first(), function ($invitee) {
            $this->assignRole($invitee->role);
            $upline = $invitee->user; 
            $upline->appendNode($this);
        });

        return $this;
    }

    public function getHandleAttribute()
    {
        return $this->extra_attributes['handle'];
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim(ucfirst($value));
    }

    public function getStatusAttribute()
    {
        return [
            'verified' => $this->isVerified(),
            'roles' => $this->roles->pluck('name')->toArray(),
        ];
    }
}
