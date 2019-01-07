<?php

namespace App;

use App\Eloquent\Phone;
use App\Contracts\Sociable;
use App\Jobs\SendUserInvitation;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\{HasNotifications, HasMobile, Askable, HasSchemalessAttributes, HasGroups, HasAreas};

class Contact extends Model implements Sociable
{
    use Notifiable;

	use HasNotifications, HasMobile, Askable, HasSchemalessAttributes, HasGroups, HasRoles, HasAreas;
	
    protected $table = 'contacts';

    protected $fillable = [
    	'mobile',
        'name',
    	'role',
        'message',
    ];

    protected $guard_name = 'campaign';

    public $casts = [
        'extra_attributes' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function($user) {
            $user->mobile   = Phone::number($user->mobile);
        });
    }

    public static function invite($mobile, $role, $driver = null) {
        return tap(static::create(compact('mobile', 'role')), function ($invitee) use ($driver) {
            $invitee->send($driver);
        });
    }

    public function send($driver = 'Telegram')
    {
        SendUserInvitation::dispatch($this, $driver);
    }

    public function upline(): MorphTo
    {
        return $this->morphTo();
    }

    public function contacts()
    {
        return $this->morphMany(Contact::class, 'upline');
    }

    public function getRewardAttribute(): int
    {
        return $this->extra_attributes['reward'] ?? 0;
    }

    public function setRewardAttribute($value): Invitee
    {
        $this->extra_attributes['reward'] = $value;

        return $this;
    }

    public function getRoleAttribute()
    {
        return $this->extra_attributes['role'] ?? null;
    }

    public function setRoleAttribute($value)
    {
        $this->extra_attributes['role'] = $value;

        return $this;
    }

    public function getMessageAttribute()
    {
        return $this->extra_attributes['message'] ?? null;
    }

    public function setMessageAttribute($value)
    {
        $this->extra_attributes['message'] = $value;

        return $this;
    }

    public function getTelerivetIdAttribute()
    {
        return $this->extra_attributes['telerivet_id'] ?? null;
    }

    public function setTelerivetIdAttribute($value): Invitee
    {
        $this->extra_attributes['telerivet_id'] = $value;

        return $this;
    }

    //put this in trait
    public function tags()
    {
        return $this->morphMany(Tag::class, 'tagger');
    }
}
