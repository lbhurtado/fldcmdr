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

}