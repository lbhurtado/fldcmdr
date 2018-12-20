<?php

namespace Tests\Unit;

use App\Tag;
use App\Group;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	/** @test */
    public function group_has_name()
    {
    	$name = $this->faker->name;
    	$group = Group::create(compact('name'));

        $this->assertEquals($group->name, $name);
        $this->assertDatabaseHas('groups', compact('name'));
    }

    public function group_is_taggable()
    {
    	$code = $this->faker->word;

    	$group = tap(factory(Group::class)->create(), function ($grp) use ($code) {
    		$grp->tags()->create(compact('code'));
    		$grp->save();
    	});

        $this->assertEquals($group->tags->first()->code, $code);
        $this->assertDatabaseHas('tags', [
        	'code' => $code,
        	'taggable_id' => $group->id,
        	'taggable_type' => get_class($group)
        ]);
    }

    /** @test */
    public function group_recursive_create_from_array()
    {
    	$node = Group::create([
    		'name' => 'top',
    		'children' => [
    			[
	    			'name' => 'middle',
	    			'children' => [
	    				['name' => 'leaf',]
	    			],
    			]
    		],
    	]);

    	$this->assertDatabaseHas('groups', ['name' => 'top']);
    	$this->assertDatabaseHas('groups', ['name' => 'middle', 'parent_id' => Group::where('name', 'top')->first()->id]);
    	$this->assertDatabaseHas('groups', ['name' => 'leaf', 'parent_id' => Group::where('name', 'middle')->first()->id]);
    }

    /** @test */
    public function group_recursive_create_from_dot_notation_with_overlap()
    {
    	Group::build(['top', 'middle', 'leaf1']);
    	Group::build(explode('.', 'top.middle.leaf2'));
    	Group::build('top.middle.leaf3');

    	$this->assertDatabaseHas('groups', ['name' => 'top']);
    	$this->assertDatabaseHas('groups', ['name' => 'middle', 'parent_id' => Group::where('name', 'top')->first()->id]);
    	$this->assertDatabaseHas('groups', ['name' => 'leaf1', 'parent_id' => Group::where('name', 'middle')->first()->id]);
    	$this->assertDatabaseHas('groups', ['name' => 'leaf2', 'parent_id' => Group::where('name', 'middle')->first()->id]);
    	$this->assertDatabaseHas('groups', ['name' => 'leaf3', 'parent_id' => Group::where('name', 'middle')->first()->id]);
    }   
}
