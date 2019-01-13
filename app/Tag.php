<?php

namespace App;

use App\{User, Group, Area, Campaign};
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSchemalessAttributes;

use App\Eloquent\TaggablePivot;

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
        // $tagger->tags()->delete();
        $tagger->removeTags();
        
        return tap(static::make($attributes), function ($tag) use ($tagger) {
            $tag->tagger()->associate($tagger);
            $tag->save();
        });
    }

    public static function validateCode($code)
    {
        $code = strtoupper($code);

        return ! is_null(static::whereCode($code)->first());
    }

    public static function withCode($code)
    {
        return optional(static::all()->filter(function ($value, $key) use ($code) {
            if (strtolower($value->code) == strtolower($code)) {
                return $value;
            }
        }))->first();
        // return static::where('code', 'ilike', trim($code))->first();
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

    public function setArea(Area $area, $detach = false)
    {
        if ($detach == true) {
            $this->areas()->detach();
        }

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

    public function newPivot(Model $parent, array $attributes, $table, $exists, $using = NULL)
    {
        if ($parent instanceof Area) {
            return new TaggablePivot($parent, $attributes, $table, $exists);
        }

        return parent::newPivot($parent, $attributes, $table, $exists);
    }

    public function groups()
    {
        return $this->morphedByMany(Group::class, 'taggable')->withTimestamps();
    }

    public function roles()
    {
        return $this->morphedByMany(Role::class, 'taggable')->withTimestamps();
    }

    public function areas()
    {
        return $this->morphedByMany(Area::class, 'taggable')->withTimestamps();
    }

    public function campaigns()
    {
        return $this->morphedByMany(Campaign::class, 'taggable')->withTimestamps();
    }
}
