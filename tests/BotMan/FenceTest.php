<?php

namespace Tests\BotMan;

use Tests\TestCase;

use App\{User, Stub};
use App\Eloquent\Phone;
use Spatie\Permission\Models\Role;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/fence';

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');

        $this->driver = 'Telegram';
        $this->channel_id = $this->faker->randomNumber(8);
        $this->center = ['longitude' => 121.030962, 'latitude' => 14.644346];

        tap(factory(User::class)->make(), function ($user) {
            $user->driver = $this->driver;
            $user->channel_id = $this->channel_id;
        })->save();
    }



    /** @test */
    public function signup_success_run()
    {
        User::where(compact('driver', 'channel_id'))
            ->first()
            ->checkin($this->center);
        
        $this->bot
            ->setUser(['id' => $this->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('signup.fence.center', $this->center))
            ;
    }
}
