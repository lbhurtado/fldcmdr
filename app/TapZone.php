<?php

namespace App;

use Malhal\Geographical\Geographical;
use Illuminate\Database\Eloquent\Model;

class TapZone extends Model
{
    use Geographical;

    protected static $kilometers = true;

    protected $fillable = [
    	'longitude', 'latitude', 'role',
    ];

    public static function generate(User $user, $role = 'subscriber')   
    {
        return tap(static::byUser($user)->firstOrNew(compact('role')), function ($model) use ($user) {
            $latestCheckin = Checkin::byUser($user)->latest()->first();
            $model->longitude = $latestCheckin->longitude;
            $model->latitude = $latestCheckin->latitude;
            $model->user()->associate($user);
            $model->save();      
            $model->refresh();      
        })->coordinates;
    }

    public static function validate($coordinates)
    {
        $stub = strtoupper($stub);

        return static::where(compact('stub'))->first() ?? false;
    }

    public function getCoordinatesAttribute(): array
    {
        return [
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
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
