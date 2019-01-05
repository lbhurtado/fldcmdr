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
}
