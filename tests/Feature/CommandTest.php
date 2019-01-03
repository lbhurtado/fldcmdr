<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\{Command, User, Contact, Group, AirTime};
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommandTest extends TestCase
{
	use RefreshDatabase, WithFaker;

    private $mobile;

    private $contact;

    private $group;

    private $airtime;

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');

        $this->mobile = '09189362340';
        $this->contact = factory(Contact::class)->create(['mobile' => $this->mobile]);
        $this->group = factory(Group::class)->create();
        $this->contact->assignGroup($this->group);
        $this->airtime = factory(AirTime::class)->create();
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
        $tag = Command::tag($this->mobile, $code);
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
    public function tag_has_airtimes_as_taggables()
    {
        $tag = Command::tag($this->mobile);
        $this->assertEquals($this->airtime->name, $tag->airtimes()->first()->name);
    }

    /** @test */
    public function claimer_has_upline_get_groups()
    {
        $tag = Command::tag($this->mobile);
        $code = $tag->code;
        $mobile = '+639171234567';
        $claimer = Command::claim($mobile, $code);
        $this->assertEquals($claimer->mobile, $mobile);
        $this->assertEquals($tag->tagger->mobile, $claimer->upline->mobile);
        $this->assertEquals($tag->groups()->first()->name, $claimer->groups()->first()->name);
    }
}
