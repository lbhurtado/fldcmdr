<?php

namespace App;

use App\Jobs\SendUserInvitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Traits\{HasNotifications, HasMobile};

class Invitation extends Model
{
    use Notifiable;

	use HasNotifications, HasMobile;
	
    protected $fillable = [
    	'mobile',
    	'role',
        'message',
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

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
