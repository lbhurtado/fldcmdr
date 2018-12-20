<?php

namespace App;

use App\Tag;
use App\Traits\NestedTrait;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
	use NestedTrait;

    protected $fillable = [
    	'name',
    ];

    public function tags()
    {
    	return $this->morphMany(Tag::class, 'taggable');
    }

    public function getQualifiedNameAttribute()
    {
    	return implode('.', tap($this->ancestors()->defaultOrder()->get()->pluck('name')->toArray(), function (&$array) {
    		array_push($array, $this->name);
    	}));
    }

    public function getQNAttribute()
    {
    	return $this->qualified_name;
    }
}
