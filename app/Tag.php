<?php

namespace App;

use App\{User, Group, Area, Campaign};
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
        $tagger->tags()->delete();
        
        return tap(static::make($attributes), function ($tag) use ($tagger) {
            $tag->tagger()->associate($tagger);
            $tag->save();
        });
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    public function tagger()
    {
        return $this->morphTo();
    }

    public function setGroup(Group $group)
    {
        $this->groups()->save($group);

        return $this;
    }

    public function setArea(Area $area)
    {
        $this->areas()->save($area);

        return $this;
    }

    public function setCampaign(Campaign $campaign)
    {
        $this->campaigns()->save($campaign);

        return $this;
    }

    public function setRole(Role $role)
    {
        $this->roles()->save($role);

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

    public function areas()
    {
        return $this->morphedByMany(Area::class, 'taggable');
    }

    public function campaigns()
    {
        return $this->morphedByMany(Campaign::class, 'taggable');
    }
}
