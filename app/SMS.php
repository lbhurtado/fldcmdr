<?php

namespace App;

use Carbon\Carbon;
use App\Eloquent\Phone;
use App\Jobs\InviteStubHolder;
use Illuminate\Database\Eloquent\Model;

class SMS extends Model
{
    const INCOMING = 'incoming_message';
    
    protected $fillable = [
		'from_number',
		'to_number',
		'message_type',
		'direction',
		'content',
		'simulated',
		'time_created',
		'time_sent',
    ];

    protected $dates = [
		'time_created',
		'time_sent',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
    	'simulated' => 'boolean',
    ];

    public function checkStubAndInvite()
    {
        InviteStubHolder::dispatch($this);
    }

    public function setFromNumberAttribute($value)
    {
        $this->attributes['from_number'] = Phone::validate($value) ?: $value;
    }

    public function setToNumberAttribute($value)
    {
        $this->attributes['to_number'] = Phone::validate($value) ?: $value;
    }

    public function setTimeCreatedAttribute($value)
    {
        $this->attributes['time_created'] = Carbon::createFromTimestamp($value);
    }

    public function setTimeSentAttribute($value)
    {
        $this->attributes['time_sent'] = Carbon::createFromTimestamp($value);
    }
}
