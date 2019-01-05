<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\{Command, User, Contact, Group, Area, Campaign};
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommandTest extends TestCase
{
	use RefreshDatabase, WithFaker;

    private $mobile;

    private $contact;

    private $group;

    private $area;

    private $campaign;

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');

        $this->mobile = '09189362340';
        $this->contact = factory(Contact::class)->create(['mobile' => $this->mobile]);
        $this->group = factory(Group::class)->create();
        $this->area = factory(Area::class)->create();
        $this->contact->assignGroup($this->group);
        $this->contact->assignArea($this->area);
        $this->campaign = factory(Campaign::class)->create();
    }

    /** @test */
    public function user_can_tag()
    {
        $mobile = '09088882786';
        $user = factory(User::class)->create(compact('mobile'));
        $tag = Command::tag($mobile);
        $this->assertEquals($tag->tagger->only('id', 'mobile'), $user->only('id', 'mobile'));
        $this->assertEquals($tag->tagger_type, User::class);
    }

    /** @test */
    public function contact_can_also_tag()
    {
        $tag = Command::tag($this->mobile);
        $this->assertEquals($tag->tagger->only('id', 'mobile'), $this->contact->only('id', 'mobile'));
        $this->assertEquals($tag->tagger_type, Contact::class);
    }

    /** @test */
    public function social_can_tag_manually()
    {
        $code = $this->faker->word;
        $tag = Command::tag($this->mobile, ['keyword' => $code]);
        $this->assertEquals(strtoupper($tag->code), strtoupper($code));
    }  

    /** @test */
    public function tag_has_groups_as_taggables()
    {
        $this->assertEquals($this->group->id, $this->contact->groups()->first()->id);

        $tag = Command::tag($this->mobile);
        $this->assertEquals($this->group->name, $tag->groups()->first()->name);
    }

    /** @test */
    public function tag_has_areas_as_taggables()
    {
        $this->assertEquals($this->area->id, $this->contact->areas()->first()->id);

        $tag = Command::tag($this->mobile);
        $this->assertEquals($this->area->name, $tag->areas()->first()->name);
    }

    /** @test */
    public function tag_has_campaigns_as_taggables()
    {
        $tag = Command::tag($this->mobile);
        $this->assertEquals($this->campaign->name, $tag->campaigns()->first()->name);
    }

    /** @test */
    public function claimer_has_upline_get_groups()
    {
        $tag = Command::tag($this->mobile);
        $code = $tag->code;
        $mobile = '639171234567';
        $claimer = Command::claim($mobile, $code);
        $this->assertEquals($claimer->mobile, $mobile);
        $this->assertEquals($tag->tagger->mobile, $claimer->upline->mobile);
        $this->assertEquals($tag->groups()->first()->name, $claimer->groups()->first()->name);
    }

    /** @test */
    public function tag_can_set_keyword_group_and_area()
    {
        $code = $this->faker->word;
        $group = factory(Group::class)->create();
        $area = factory(Area::class)->create();

        $tag = Command::tag($this->mobile, [
            'keyword' => $code,
            'group' => $group->name,
            'area' => $area->name, 
        ]);

        $this->assertEquals(strtoupper($tag->code), strtoupper($code));
        $this->assertEquals($group->name, $tag->groups()->first()->name);
        $this->assertEquals($area->name, $tag->areas()->first()->name);
    }
}
