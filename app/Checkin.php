<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    // protected static $kilometers = true;
    
    protected $fillable = [
    	'longitude',
    	'latitude',
        'location',
    	'remarks',
    ];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
