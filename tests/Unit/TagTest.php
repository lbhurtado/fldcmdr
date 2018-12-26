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
    public function tag_has_code_user_and_taggable()
    {
        $user = factory(User::class)->create();
    	$group = factory(Group::class)->create();
    	$code = $this->faker->word;

        $tag = Tag::createWithUserAndGroup(compact('code'), $user, $group);

    	$this->assertEquals($tag->code, strtoupper($code));
    	$this->assertEquals($tag->user->id, $user->id);
        $this->assertDatabaseHas('tags', [
        	'code' => strtoupper($code),
            'user_id' => $user->id,
        	'taggable_id' => $group->id,
        	'taggable_type' => get_class($group)
        ]);
    }
}
