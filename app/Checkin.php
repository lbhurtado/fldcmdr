<?php

namespace App;

use App\Jobs\ReverseGeocode;
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

    public function reverseGeocode()
    {
        ReverseGeocode::dispatch($this);
    }
}
