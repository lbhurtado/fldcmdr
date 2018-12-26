<?php

namespace App;

use App\{User, Group};
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSchemalessAttributes;

class Tag extends Model
{
	use HasSchemalessAttributes;

    protected $fillable = [
    	'code',
    ];

    public $casts = [
        'extra_attributes' => 'array',
    ];

    public static function createWithTagger($attributes, $tagger)
    {
        return tap(static::make($attributes), function ($tag) use ($tagger) {
            $tag->tagger()->associate($tagger);
            $tag->save();
        });
    }

    public function tagger()
    {
        return $this->morphTo();
    }

    public function setGroup(Group $group)
    {
        $this->groups()->save($group);
        $this->save();

        return $this;
    }

    public function setRole(Role $role)
    {
        $this->roles()->save($role);
        $this->save();

        return $this;
    }

    public function groups()
    {
        return $this->morphedByMany(Group::class, 'taggable');
    }

    public function roles()
    {
        return $this->morphedByMany(Role::class, 'taggable');
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }
}
