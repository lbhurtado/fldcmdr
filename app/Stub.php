<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stub extends Model
{
    protected $fillable = [
    	'stub', 'role',
    ];

    public static function generate(User $user, $role = 'subscriber')   
    {
        return tap(Stub::byUser($user)->firstOrNew(compact('role')), function ($model) use ($user) {
            $model->stub = str_random(6);
            $model->user()->associate($user);
            $model->save();            
        })->stub;
    }

    public static function validate($stub)
    {
        $stub = strtoupper($stub);

        return static::where(compact('stub'))->first() ?? false;
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function setStubAttribute($value)
    {
    	return $this->attributes['stub'] = strtoupper($value);
    }

    public function scopeByUser($query, User $user)
    {
        $user_id = $user->id;

        return $query->where(compact('user_id'));
    }
}
