<?php

namespace Tests\BotMan;

use Tests\TestCase;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/start';

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');
    }

    /** @test */
    public function onboarding_success_run()
    {
        $driver = 'Telegram';
        $channel_id = $this->faker->randomNumber(8);

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('onboarding.introduction.1'))
            ->assertReply(trans('onboarding.introduction.2'))
            ->assertReply(trans('onboarding.introduction.3'))
            ->assertReply(trans('onboarding.introduction.4'))
            ->assertQuestion(trans('onboarding.input.optin'))
            ;
    }
}
