<?php

namespace Tests\BotMan;

use Tests\TestCase;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatusTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = "/status";

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');
    }

    /** @test */
    public function broadcast_success_run()
    {
        $driver = 'Telegram';
        $channel_id = $this->faker->randomNumber(8);
        $message = $this->faker->sentence;

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ;
    }
}
