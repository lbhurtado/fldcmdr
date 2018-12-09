<?php

namespace App;

use App\Jobs\SendUserInvitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\{HasNotifications, HasMobile, Askable, HasSchemalessAttributes};

class Invitee extends Model
{
    use Notifiable;

	use HasNotifications, HasMobile, Askable, HasSchemalessAttributes;
	
    protected $table = 'invitees';

    protected $fillable = [
    	'mobile',
    	'role',
        'message',
    ];

    public $casts = [
        'extra_attributes' => 'array',
    ];

    public static function invite($mobile, $role, $driver = null) {
        return tap(static::create(compact('mobile', 'role')), function ($invitee) use ($driver) {
            $invitee->send($driver);
        });
    }

    public function send($driver = 'Telegram')
    {
        SendUserInvitation::dispatch($this, $driver);
    }

    public function user(): BelongsTo
    {
    	return $this->belongsTo(User::class);
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
}
