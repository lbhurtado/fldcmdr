<?php

namespace Tests\BotMan;

use Tests\TestCase;

use App\User;
use App\Eloquent\Phone;
use Spatie\Permission\Models\Role;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InviteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/invite';

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');

        $this->dbseed();
    }

    function dbseed()
    {
        $roles = ['admin', 'operator', 'staff', 'worker', 'subscriber'];
        foreach ($roles as $name) {
            Role::create(compact('name'));
        }        
    }
    /** @test */
    public function invite_success_run()
    {
        $channel_id = $this->faker->randomNumber(8);
        $mobile = Phone::number('09178251991');
        $pin = $this->faker->randomNumber(6);
        $code = 'operator';

        \Queue::fake();
        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('invite.introduction'))
            ->assertQuestion(trans('invite.input.mobile'))
            ->receives($mobile)
            ->assertQuestion(trans('invite.input.role'))
            ->receivesInteractiveMessage($code)
            ->assertReply(trans('invite.processing'))
            ;

        $this->assertEquals($user->parent->id, $this->parent->id);
        
        \Queue::assertPushed(\App\Jobs\SendUserInvitation::class);   
    }
}
