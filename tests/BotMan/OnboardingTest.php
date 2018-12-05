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

    private $random_keyword;

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');
        $this->random_keyword = $this->faker->sentence;
    }

    /** @test */
    public function onboarding_success_run()
    {
        $driver = 'Telegram';
        $channel_id = $this->faker->randomNumber(8);

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->random_keyword)
            ->assertReply(trans('onboarding.introduction.1'))
            ->assertReply(trans('onboarding.introduction.2'))
            ->assertReply(trans('onboarding.introduction.3'))
            ->assertReply(trans('onboarding.introduction.4'))
            ->assertQuestion(trans('onboarding.question.optin'))
            ->receivesInteractiveMessage('yes')
            ->assertQuestion(trans('onboarding.question.stub'))
            ->receivesInteractiveMessage('yes')
            ->assertReply(trans('onboarding.processing'))
            ->assertReply(trans('onboarding.processed'))
            ->assertReply(trans('signup.introduction'))
            ;
    }

    /** @test */
    public function onboarding_success_run_no_stub()
    {
        $driver = 'Telegram';
        $channel_id = $this->faker->randomNumber(8);

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->random_keyword)
            ->assertReply(trans('onboarding.introduction.1'))
            ->assertReply(trans('onboarding.introduction.2'))
            ->assertReply(trans('onboarding.introduction.3'))
            ->assertReply(trans('onboarding.introduction.4'))
            ->assertQuestion(trans('onboarding.question.optin'))
            ->receivesInteractiveMessage('yes')
            ->assertQuestion(trans('onboarding.question.stub'))
            ->receivesInteractiveMessage('no')
            ->assertReply(trans('onboarding.processing'))
            ->assertReply(trans('onboarding.processed'))
            ->assertReply(trans('verify.introduction'))
            ;
    }

    /** @test */
    public function onboarding_decline()
    {
        $driver = 'Telegram';
        $channel_id = $this->faker->randomNumber(8);

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->random_keyword)
            ;

        $this->assertDatabaseHas('users', compact('driver', 'channel_id'));
        
        $this->bot
            ->assertReply(trans('onboarding.introduction.1'))
            ->assertReply(trans('onboarding.introduction.2'))
            ->assertReply(trans('onboarding.introduction.3'))
            ->assertReply(trans('onboarding.introduction.4'))
            ->assertQuestion(trans('onboarding.question.optin'))
            ->receivesInteractiveMessage('no')
            ->assertReply(trans('onboarding.regrets'))
            ->assertReply(trans('onboarding.expunge'))
            ;
        
        $this->assertDatabaseMissing('users', compact('driver', 'channel_id'));
    }
}
