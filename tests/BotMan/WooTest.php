<?php

namespace Tests\BotMan;

use Tests\TestCase;

use App\{User, Stub};
use App\Eloquent\Phone;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WooTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/woo';

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');
    }

    /** @test */
    public function signup_success_run()
    {
        $driver = 'Telegram';
        $channel_id = $this->faker->randomNumber(8);
        $stub = $this->faker->shuffle('LESTER');

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ;

        $user = User::where(compact('driver', 'channel_id'))->first();
        $stub = Stub::where('user_id', $user->id)->first()->stub;

        $this->bot
            ->assertReply(trans('signup.woo.stub', compact('stub')))
            ;   
    }
}
