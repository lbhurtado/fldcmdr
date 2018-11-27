<?php

namespace Tests\BotMan;

use Tests\TestCase;

use App\{User, Stub};
use App\Eloquent\Phone;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SignupTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/signup';

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');
    }

    /** @test */
    public function signup_success_run()
    {
        $admin = factory(User::class)->create();
        $stub = Stub::generate($admin);
        $mobile = Phone::number('09178251991');
        $pin = $this->faker->randomNumber(6);

        $channel_id = $this->faker->randomNumber(8);
        // $mode = 'stub';

        \Queue::fake();
        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('signup.introduction'))
            // ->assertQuestion(trans('signup.input.mode'))
            // ->receives($mode)
            ->assertQuestion(trans('signup.input.stub'))
            ->receivesInteractiveMessage($stub)
            ->assertReply(trans('signup.processing'))
            ->assertReply(trans('signup.processed'))
            ->assertReply(trans('verify.introduction'))
            ->assertQuestion(trans('verify.input.mobile'))
            ->receives($mobile)
            ->assertQuestion(trans('verify.input.pin'))
            ->receives('123456')
            ;

        $user = User::withMobile($mobile)->first();
        $user->verify($pin, false);

        $this->assertTrue($user->isVerified());
        // if (config('chatbot.reward.enabled'))
            // $this->bot->assertReply(trans('verify.reward'));
        
        // \Queue::assertPushed(\App\Jobs\SendUserInvitation::class);   
    }
}
