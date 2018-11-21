<?php

namespace Tests\BotMan;

use Tests\TestCase;

use App\User;
use App\Invitation;
use App\Eloquent\Phone;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SurveyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/survey';

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    /** @test */
    public function survey_success_run()
    {
        $channel_id = $this->faker->randomNumber(8);
        $mobile = Phone::number('09178251991');

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('survey.introduction'))
            ;
    }
}
