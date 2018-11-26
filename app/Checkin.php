<?php

namespace App;

use App\Jobs\ReverseGeocode;
use Malhal\Geographical\Geographical;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    use Geographical;
    
    protected static $kilometers = true;
    
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

    public function scopeByUser($query, User $user)
    {
        $user_id = $user->id;

        return $query->where(compact('user_id'));
    }
}
