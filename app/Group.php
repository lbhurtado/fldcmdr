<?php

namespace App;

use App\Tag;
use App\Traits\NestedTrait;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
	use NestedTrait;

    protected $glue = ':';

    protected $pieces = 'title';

    protected $fillable = [
    	'name',
    ];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
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
