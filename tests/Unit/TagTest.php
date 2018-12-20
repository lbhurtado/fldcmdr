<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\{Tag, Group};
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	/** @test */
    public function tag_has_code_description_and_taggable()
    {
    	$group = factory(Group::class)->create();
    	$code = $this->faker->word;
    	$description = $this->faker->sentence;

    	$tag = tap(Tag::make(compact('code', 'description')), function ($tag) use ($group) {
	    	$tag->taggable()->associate($group);
	    	$tag->save();    		
    	});

    	$this->assertEquals($tag->code, $code);
    	$this->assertEquals($tag->description, $description);
        $this->assertDatabaseHas('tags', [
        	'code' => $code,
        	'description' => $description,
        	'taggable_id' => $group->id,
        	'taggable_type' => get_class($group)
        ]);
    }
}
