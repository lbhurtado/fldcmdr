<?php

namespace App\Traits;

use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

trait NestedTrait
{
	use NodeTrait; 

	public static function build($nodes, self $parent = null)
    {
    	return static::create(build_nested_nodes($nodes), $parent);
    }

    //override create in Kalnoy NestedSet
    public static function create(array $attributes = [], self $parent = null)
    {
        $children = array_pull($attributes, 'children');

        $instance = static::firstOrNew($attributes);

        if ($parent) {
            $instance->appendToNode($parent);
        }

        $instance->save();

        // Now create children
        $relation = new EloquentCollection;

        foreach ((array)$children as $child) {
            $relation->add($child = static::create($child, $instance));

            $child->setRelation('parent', $instance);
        }

        $instance->refreshNode();

        return $instance->setRelation('children', $relation);
    }

    public function getDottedNameAttribute()
    {
        $glue = '.';
        $default = ! property_exists(static::class, 'default') ? true : $this->deault;
        $pieces = 'name';

        return implode($glue, $this->lineage($pieces, $default));
    }

    public function getQualifiedNameAttribute()
    {
        $glue = ! property_exists(static::class, 'glue') ? ', ' : $this->glue;
        $default = ! property_exists(static::class, 'default') ? true : $this->deault;
        $pieces = ! property_exists(static::class, 'pieces') ? 'title' : $this->pieces;

        return implode($glue, $this->lineage($pieces, $default));
    }

    public function getQNAttribute()
    {
        return $this->qualified_name;
    }

    public function getTitleAttribute()
    {
        return $this->attributes['name'] = title_case(str_replace('_', ' ', $this->attributes['name']));
    }

    protected function lineage($pieces, $default = true)
    {
        // $pieces = ! property_exists(static::class, 'pieces') ? 'title' : $this->pieces;

        $array = tap($this->ancestors()->defaultOrder()->get()->pluck($pieces)->toArray(), function (&$array) use ($pieces) {
            array_push($array, $this->{$pieces});
        }); 

        return $default ? $array : array_reverse($array);
    }
}