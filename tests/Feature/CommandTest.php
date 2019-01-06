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

        // $this->mobile = '09189362340';
                $this->mobile = $this->faker->mobileNumber;
        $this->contact = factory(Contact::class)->create(['mobile' => $this->mobile]);
        $this->group = factory(Group::class)->create();
        $this->area = factory(Area::class)->create();
        $this->contact->assignGroup($this->group);
        $this->contact->assignArea($this->area);
        $this->campaign = tap(factory(Campaign::class)->create(), function ($campaign) {
            $campaign->message = $this->faker->sentence;
            $campaign->air_time = 25;
        });
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
    public function tag_can_set_keyword_group_area_and_campaign()
    {
        $code = $this->faker->word;
        $group = factory(Group::class)->create();
        $area = factory(Area::class)->create();
        $campaign = tap(factory(Campaign::class)->create(), function ($campaign) {
            $campaign->message = $this->faker->sentence;
            $campaign->air_time = 25;
        });

        $tag = Command::tag($this->mobile, [
            'keyword' => $code,
            'group' => $group->name,
            'area' => $area->name, 
            'campaign' => $campaign->name,
        ]);

        $this->assertEquals(strtoupper($tag->code), strtoupper($code));
        $this->assertEquals($group->name, $tag->groups()->first()->name);
        $this->assertEquals($area->name, $tag->areas()->first()->name);
        $this->assertEquals($campaign->name, $tag->campaigns()->first()->name);
    }

    /** @test */
    public function claimer_has_group_of_upline()
    {
        \Queue::fake();

        $tag = Command::tag($this->mobile);
        $code = $tag->code;
        $mobile = $this->faker->mobileNumber;
        $claimer = Command::claim($mobile, $code);
        $this->assertEquals($claimer->mobile, \App\Eloquent\Phone::smsc($mobile));
        $this->assertEquals($tag->tagger->mobile, $claimer->upline->mobile);
        $this->assertEquals($tag->groups()->first()->name, $claimer->groups()->first()->name);
        // $this->assertEquals($tag->areas()->first()->name, $claimer->areas()->first()->name);
        // $this->assertEquals($tag->campaigns()->first()->name, $claimer->campaigns()->first()->name);
    }

    /** @test */
    public function claimer_can_get_keyword_group_area_and_send_campaign()
    {
        \Queue::fake();

        $tag = Command::tag($this->mobile, [
            'group' => $this->group->name,
            'area' => $this->area->name, 
            'campaign' => $this->campaign->name,
        ]);

        $claimer = Command::claim($this->faker->mobileNumber, $tag->code);
        $this->assertEquals($this->group->name, $claimer->groups()->first()->name);
        $this->assertEquals($this->area->name, $claimer->areas()->first()->name);

        \Queue::assertPushed(\App\Jobs\SendCampaign::class);
    }

    /** @test */
    public function claimer_can_tag_and_so_on_and_so_forth()
    {
        \Queue::fake();

        $tag1 = Command::tag($this->mobile, [
            'group' => $this->group->name,
            'area' => $this->area->name, 
            'campaign' => $this->campaign->name,
        ]);

        $claimer1 = Command::claim($this->faker->mobileNumber, $tag1->code);

        $tag2 = Command::tag($claimer1->mobile);
        $claimer2 = Command::claim($this->faker->mobileNumber, $tag2->code);

        $tag3 = Command::tag($claimer2->mobile);
        $claimer3 = Command::claim($this->faker->mobileNumber, $tag3->code);

        $this->assertEquals($this->group->name, $claimer2->groups()->first()->name);
        $this->assertEquals($this->area->name, $claimer2->areas()->first()->name);

        $this->assertEquals($this->group->name, $claimer3->groups()->first()->name);
        $this->assertEquals($this->area->name, $claimer3->areas()->first()->name);

        $group = factory(Group::class)->create();
        $tag4 = Command::tag($claimer2->mobile, [
            'group' => $group->name,
        ]);
        $claimer4 = Command::claim($this->faker->mobileNumber, $tag4->code);
        $this->assertNotEquals($this->group->name, $claimer4->groups()->first()->name);
        $this->assertEquals($group->name, $claimer4->groups()->first()->name);
        $this->assertEquals($this->area->name, $claimer4->areas()->first()->name);

        \Queue::assertPushed(\App\Jobs\SendCampaign::class);
    }

}
