<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\{Tag, Group, User, Area, Campaign};
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
        $area = factory(Area::class)->create();
        $campaign = factory(Campaign::class)->create();
        
        $this->assertEquals($tag->groups()->count(), 0);
        $this->assertEquals($tag->areas()->count(), 0);
        $this->assertEquals($tag->campaigns()->count(), 0);

        $tag->setGroup($group);
        $tag->setArea($area);
        $tag->setCampaign($campaign);

        $this->assertEquals($tag->groups()->count(), 1);
        $this->assertEquals($tag->areas()->count(), 1);
        $this->assertEquals($tag->campaigns()->count(), 1);

        $this->assertEquals($tag->groups()->first()->name, $group->name);
        $this->assertEquals($tag->areas()->first()->name, $area->name);
        $this->assertEquals($tag->campaigns()->first()->name, $campaign->name);

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_id' => $group->id,
            'taggable_type' => get_class($group),
        ]);

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_id' => $area->id,
            'taggable_type' => get_class($area),
        ]);

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_id' => $campaign->id,
            'taggable_type' => get_class($campaign),
        ]);
    }
}
