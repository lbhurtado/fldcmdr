<?php

namespace Tests\BotMan;

use Tests\TestCase;

use App\User;
use App\Helpers\Phone;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VerifyTest extends TestCase
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
    public function verify_success_run()
    {
        $channel_id = $this->faker->randomNumber(8);
        $mobile = Phone::number('09178251991');
        $pin = $this->faker->randomNumber(6);

        \Queue::fake();
        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('verify.introduction'))
            ->assertQuestion(trans('verify.input.mobile'))
            ->receives($mobile)
            ;

        \Queue::assertPushed(\App\Jobs\RequestOTP::class);   

        $user = User::withMobile($mobile)->first();

        $this->assertEquals($user->mobile, $mobile);
        $this->assertFalse($user->isVerified());

        $this->bot
            ->assertQuestion(trans('verify.input.pin'))
            ->receives('123456')
            ;

        $user->verify($pin, false);

        $this->bot
            ->assertQuestion(trans('verify.input.pin'))
            ->receives($pin)
            ;

        $this->assertTrue($user->isVerified());
        $this->bot
            ->assertReply(trans('verify.success'))
            ;
    }
}
