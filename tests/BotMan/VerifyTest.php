<?php

namespace Tests\BotMan;

use Tests\TestCase;

use App\User;
use App\Invitee;
use App\Eloquent\Phone;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VerifyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/verify';

    private $admin;

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');
        $mobile = env('ADMIN_MOBILE', decrypt(
            'eyJpdiI6InBNQzljQVZiaUd4Vk1LUmlcL1orMG5BPT0iLCJ2YWx1ZSI6IkFxRnZLWnI2RlpaWXNFU2hGV3N5S0hKdUgzSEtlWVFIQTJqd1dDcnNSMGs9IiwibWFjIjoiNzEzM2ZlZmI2NTE2ZWFiY2Y5MWMzYzYyMjc5ODRmMGVhOTgxZDcyNGJlMTYzYmM5NmY1ZWMzYjYxMjcwZDliNSJ9'));
        $mobile = Phone::number($mobile);
        $name = env('ADMIN_NAME', decrypt(
            'eyJpdiI6InZkRUpOYXdoSENKOTkzYTBBYmoyVnc9PSIsInZhbHVlIjoibDQyRWFIcmVEWWEwcmhjdGhualdxQ2c1ZE5CTVB5NzlUTFdieWN2SjFRdz0iLCJtYWMiOiIxMTMwZmRlYzFkZjhlMGE2NjA1NzUyMjFiNmI3NDQ5NjNhMjdmY2U1YTdiMzk2ODVjNGQ0YzVkNDliNjY3OTllIn0='));
        $password = '$2y$10$gz7MXG5YLhKykthNDjkfWu.fV80v.WpS..xn3T5SOza2Vo7tfGHtG';
        $email = 'lester@hurtado.ph';
        $driver = 'Telegram';
        $channel_id = '592028290';
        $telerivet_id = 'CT0e9dae8539be5222'; //careful, this might be regenerated
        $this->admin = User::create(compact('mobile', 'name', 'password', 'email', 'driver', 'channel_id', 'telerivet_id'));
        $this->admin->makeRoot()->save();
        $this->admin->assignRole('admin');

        // $admin = factory(User::class)->create(['mobile' => '+639173011987']);
        $mobile = Phone::number('09178251991');
        $role = 'worker';
        $invitee = $this->admin->invitees()->create(compact('mobile', 'role'));
    }

    /** @test */
    public function verify_success_run()
    {
        $channel_id = $this->faker->randomNumber(8);
        $mobile = Phone::number('09178251991');
        $pin = $this->faker->randomNumber(6);

        $this->assertDatabaseMissing('users', compact('mobile'));
        $this->assertDatabaseHas('invitees', compact('mobile'));

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
        if (config('chatbot.reward.enabled'))
            $this->bot->assertReply(trans('verify.reward'))
            ;  
            
        \Queue::assertPushed(\App\Jobs\SendAskableReward::class);

        $this->bot
            ->assertReply(trans('verify.success'))
            ;
    }

    /** @test */
    public function verify_admin()
    {
        config(['chatbot.reward.enabled' => true]);
        \Queue::fake();
        $this->bot
            ->setUser(['id' => $this->admin->channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('verify.introduction'))
            ->assertQuestion(trans('verify.input.mobile'))
            ->receives($this->admin->mobile)
            ;
        
        $this->admin->verify('123456', false);

        $this->bot
            ->assertQuestion(trans('verify.input.pin'))
            ->receives('123456')
            ->assertReply(trans('verify.reward'))
            ->assertReply(trans('verify.success'))
            ;
        
        $this->assertTrue($this->admin->isVerified());

        \Queue::assertPushed(\App\Jobs\SendAskableReward::class);
    } 
}
