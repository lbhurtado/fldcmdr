<?php

namespace App;

use Malhal\Geographical\Geographical;
use Illuminate\Database\Eloquent\Model;

class TapZone extends Model
{
    use Geographical;

    protected $fillable = [
    	'longitude', 'latitude', 'role',
    ];

    public static function generate(User $user, $role = 'subscriber')   
    {
        return 'testing';
        // return tap(static::byUser($user)->firstOrNew(compact('role')), function ($model) use ($user) {
            // $checkin = Checkin::byUser($user)->latest()->first();
            // $model->longitude = $checkin->longitude;
            // $model->latitude = $checkin->latitude;
            // $model->user()->associate($user);
            // $model->save();            
        // })->coordinates;
    }

    public static function validate($coordinates)
    {
        $stub = strtoupper($stub);

        return static::where(compact('stub'))->first() ?? false;
    }

    public function getCoordinatesAttribute(): array
    {
        return = [
            'longitude' => 121.030962,
            'latitide' => 14.644346,
        ];

        return [
            'longitude' => $this->longitude,
            'latitide' => $this->latitide,
        ];
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function scopeByUser($query, User $user)
    {
        $user_id = $user->id;

        return $query->where(compact('user_id'));
    }
}
