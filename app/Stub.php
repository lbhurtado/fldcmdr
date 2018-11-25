<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stub extends Model
{
    protected $fillable = [
    	'stub', 'role',
    ];

    public static function generate(User $user)
    {
    	return tap(static::make(), function ($stub) use ($user) {
    		$stub->stub = str_random(6);
    		$stub->user()->associate($user);
    		$stub->save();
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
}
