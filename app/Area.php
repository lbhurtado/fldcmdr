<?php

namespace App;

use App\Traits\NestedTrait;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
	use NestedTrait;

    protected $fillable = [
    	'name',
    ];

    protected $default = false; 

	public function getProperNameAttribute()
    {
    	if ($this->parent)
    		return $this->title . ', ' . $this->parent->title;
    	else
    		return $this->title;
    }

    public static function withName($name)
    {
        return optional(static::all()->filter(function ($value, $key) use ($name) {
            if (strtolower($value->name) == strtolower($name)) {
                return $value;
            }
        }))->first();
        // return static::where('name', 'ilike', trim($name))->first();
    }
}
