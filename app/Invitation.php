<?php

namespace App;

use App\Jobs\SendUserInvitation;
use App\Traits\HasNotifications;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Invitation extends Model
{
    use Notifiable;

	use HasNotifications;
	
    protected $fillable = [
    	'mobile',
    	'role',
        'message',
    ];

    public function send($driver = 'Telegram')
    {
        SendUserInvitation::dispatch($this, $driver);
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
