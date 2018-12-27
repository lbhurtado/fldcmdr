<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\{Tag, Group, User};
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	/** @test */
    public function tag_has_code_and_tagger()
    {
        $tagger = factory(User::class)->create();
    	$code = $this->faker->word;
        $attributes = compact('code');

        $tag = Tag::createWithTagger($attributes, $tagger);

    	$this->assertEquals($tag->code, strtoupper($code));
    	$this->assertEquals($tag->tagger->id, $tagger->id);
        $this->assertDatabaseHas('tags', [
        	'code' => strtoupper($code),
            'tagger_id' => $tagger->id,
        	'tagger_type' => get_class($tagger)
        ]);
    }

    /** @test */
    public function tag_has_taggables_pivot()
    {
        $tag = factory(Tag::class)->create();
        $group = factory(Group::class)->create();
        $role = factory(config('permission.models.role'))->create();
        
        $this->assertEquals($tag->groups()->count(), 0);
        $this->assertEquals($tag->roles()->count(), 0);

        $tag->setGroup($group);
        $tag->setRole($role);

        $this->assertEquals($tag->groups()->count(), 1);
        $this->assertEquals($tag->roles()->count(), 1);

        $this->assertEquals($tag->groups()->first()->name, $group->name);
        $this->assertEquals($tag->roles()->first()->name, $role->name);

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_id' => $group->id,
            'taggable_type' => get_class($group),
        ]);

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_id' => $role->id,
            'taggable_type' => get_class($role),
        ]);

    }
}
