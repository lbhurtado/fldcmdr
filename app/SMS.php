<?php

namespace App;

use App\Eloquent\Phone;
use App\Jobs\InviteStubHolder;
use Illuminate\Database\Eloquent\Model;

class SMS extends Model
{    
    protected $fillable = [
        'from',
        'to',
        'message',
    ];

    public function checkStubAndInvite()
    {
        InviteStubHolder::dispatch($this);
    }

    public function setFromAttribute($value)
    {
        $this->attributes['from'] = Phone::number($value);
    }

    public function setToAttribute($value)
    {
        $this->attributes['to'] = Phone::number($value);
    }
}
