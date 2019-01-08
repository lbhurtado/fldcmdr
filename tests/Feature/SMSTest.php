<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Eloquent\Phone;
use App\{SMS, User, Tag};
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SMSTest extends TestCase
{
	use RefreshDatabase, WithFaker;

    private $from;

    private $to;

    function setUp()
    {
        parent::setUp();
        $this->faker = $this->makeFaker('en_PH');

        $this->from = Phone::number($this->faker->mobileNumber);
        $this->to = Phone::number($this->faker->mobileNumber);
    }

    /** @test */
    public function sms_will_ignore_command_if_subscriber_is_not_in_system()
    {
    	$this->assertDatabaseMissing('users', ['mobile' => $this->from]);
    	$this->assertDatabaseMissing('contacts', ['mobile' => $this->from]);

    	$message = '#';
    	$sms = SMS::create($this->getAttributes(compact('message')));

    	$this->assertDatabaseMissing('users', ['mobile' => $this->from]);
    	$this->assertDatabaseMissing('contacts', ['mobile' => $this->from]);
    }

    /** @test */
    public function sms_will_process_command_if_subscriber_is_in_system()
    {
    	$mobile = Phone::number($this->faker->mobileNumber);
    	$user = factory(User::class)->create(compact('mobile'));

    	$this->assertDatabaseHas('users', compact('mobile'));

    	$message = '#';
    	$sms = SMS::create($this->getAttributes(compact('message')));

    	// $this->assertDatabaseMissing('users', ['mobile' => $this->from]);
    	// $this->assertDatabaseMissing('contacts', ['mobile' => $this->from]);
    }

    protected function getAttributes($array = [])
    {
        $attributes = [
            'message' => $this->faker->paragraph,
            'from' => $this->from,
            'to' => $this->to,
        ];

        return array_merge($attributes, $array);
    }
}
