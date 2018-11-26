<?php

namespace App;

// use App\Eloquent\Phone;
use App\Invitation;
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
        optional(Invitation::withMobile($this->mobile)->first(), function ($invitee) {
            $this->assignRole($invitee->role);
            $upline = $invitee->user; 
            $upline->appendNode($this);
        });

        return $this;
    }
}
