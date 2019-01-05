<?php

namespace App;

use App\Contact;
use App\Eloquent\Phone;
use App\Contracts\Sociable;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\{HasNotifications, 
                Verifiable, 
                HasSchemalessAttributes, 
                HasMobile, 
                Askable,
                HasGroups,
                HasAreas
            };

class User extends Authenticatable implements Sociable
{
    use Notifiable;

    use HasNotifications, Verifiable, HasSchemalessAttributes, HasRoles, NodeTrait, HasMobile, Askable, HasGroups, HasAreas;

    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'driver', 'channel_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public $casts = [
        'extra_attributes' => 'array',
                 'status'  => 'array',
                    'info' => 'array',
    ];

    protected $guard_name = 'web';

    protected static function boot()
    {
        parent::boot();

        static::creating(function($user) {
            // optional($user->mobile, function ($mobile) {
            //     $user->mobile   = Phone::number($mobile);
            // });
            $user->name     = $user->name ?? "$user->driver.$user->channel_id";
            $user->email    = $user->name . '@' . config('chatbot.default.domain_name');
            $user->password = $user->password ?? bcrypt(config('chatbot.default.password'));
            $user->extra_attributes->handle = str_slug($user->name, '.'); 
            $user->extra_attributes->wants_notifications = false;
        });
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
        optional(Contact::withMobile($this->mobile)->first(), function ($invitee) {
            $this->assignRole($invitee->role);
            // $upline = $invitee->user; 
            $upline = $invitee->upline; 
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

    public function getInfoAttribute()
    {
        return [
            'name' => $this->name,
            'handle' => $this->handle,
            'mobile' => $this->mobile,
        ];
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }
    
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'upline');
        // return $this->hasMany(Contact::class);
    }
}
