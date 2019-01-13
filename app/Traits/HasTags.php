<?php

namespace App\Traits;

use App\Tag;
use Illuminate\Support\Str;

trait HasTags
{
    public function tags()
    {
        return $this->morphMany(Tag::class, 'tagger');
    }

    public function removeTags()
    {
    	$this->tags->each(function ($tag) {
    		$this->removeTaggables($tag, [])->delete();
    	});
    }

    public function removeTaggables(Tag $tag, ...$indices)
    {
    	$indices = $this->process($indices);

		foreach ($indices as $index) {
			$relation = str_plural(Str::snake($index));
			$tag->{$relation}()->detach();
		};

		return $tag;
    }

    protected function process($indices)
    {
    	$indices = array_flatten($indices);

    	return (count($indices) == 0)
    		   ? ['area', 'role', 'group', 'campaign']
    		   : $indices
    		   ;
    }
}