<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\Eloquent\Phone;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class CheckinTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/checkin';

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');
    }

    /** @test */
    public function checkin_success_run()
    {
        $channel_id = $this->faker->randomNumber(8);
        $mobile = Phone::number('09178251991');

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('checkin.introduction'))
            ->assertTemplate(OutgoingMessage::class)
            ;
    }
}
