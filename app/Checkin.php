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

        return $this;
    }

    public function scopeByUser($query, User $user)
    {
        $user_id = $user->id;

        return $query->where(compact('user_id'));
    }

    public function scopeNearest($query)
    {
        return $query->orderBy('distance', 'ASC');
    }

    public function getNearestTapZone()
    {
        return TapZone::distance($this->latitude, $this->longitude)->nearest()->first();
    }

    public function hydrateUserFromTapZone()
    {
        optional($this->getNearestTapZone(), function ($tap_zone) {
            if ($tap_zone->getDistanceFrom($this) <= config('chatbot.tap_zone.distance')) {
                $this->user->assignRole('subscriber');
                $upline = $tap_zone->user; 
                $upline->appendNode($this->user);
                $this->save();                
            }
        });

        return $this;
    }

    public function locatable():MorphTo
    {
        return $this->morphTo();
    }
}
