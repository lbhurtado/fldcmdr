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

class WooTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/woo';

    protected $admin;

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');

        // $this->admin = tap(factory(User::class)->create(), function($user) {
        //     $user->mobile = Phone::number('09178251991');
        //     $user->save();
        // });

    }

    /** @test */
    public function signup_success_run()
    {
        $driver = 'Telegram';
        $channel_id = $this->faker->randomNumber(8);
        $mode = 'stub';
        $stub = 'ABC123';

        // \Queue::fake();
        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ;

        $user = User::where(compact('driver', 'channel_id'))->first();
        $stub = Stub::where('user_id', $user->id)->first()->stub;

        $this->bot
            ->assertReply(trans('signup.woo.stub', compact('stub')))
            // ->assertQuestion(trans('signup.input.mode'))
            // ->receives($mode)
            // ->assertQuestion(trans('signup.input.role'))
            // ->receivesInteractiveMessage($code)
            // ->assertReply(trans('invite.processing'))
            ;
        
        // \Queue::assertPushed(\App\Jobs\SendUserInvitation::class);   
    }
}
