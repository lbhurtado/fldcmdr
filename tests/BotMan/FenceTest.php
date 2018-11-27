<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\Eloquent\Phone;
use App\{User, TapZone, Checkin};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class FenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/fence';

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');

        $this->driver = 'Telegram';
        $this->channel_id = $this->faker->randomNumber(8);

        $this->center = ['longitude' => 121.030962, 'latitude' => 14.644346];

        tap(factory(User::class)->make(), function ($user) {
            $user->driver = $this->driver;
            $user->channel_id = $this->channel_id;
        })->save();
    }



    /** @test */
    public function fence_success_run()
    {
        $driver = 'Telegram';
        $admin_channel_id = $this->faker->randomNumber(8);
        $admin = tap(factory(User::class)->make(), function ($user) use ($driver, $admin_channel_id) {
            $user->driver = $driver;
            $user->channel_id = $admin_channel_id;
            $user->save();
        });

        $center = [
            'latitude' => 14.644346,
            'longitude' => 121.030962,
        ];

        \Queue::fake();
        $this->bot
            ->setUser(['id' => $admin_channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives("/checkin")
            ->assertReply(trans('checkin.introduction'))
            ->assertTemplate(OutgoingMessage::class)
            ->receivesLocation($center['latitude'], $center['longitude'])
            ->assertTemplate(Question::class)
            ->receives("#fence")
            ->assertReply(trans('checkin.processing'))
            ->assertReply(trans('checkin.processed'))
            ->assertReply(trans('signup.fence.center', $center))
            ;

        \Queue::assertPushed(\App\Jobs\ReverseGeocode::class);

        $attributes = array_merge(['user_id' => $admin->id, 'role' => 'subscriber'], $center);
        $this->assertDatabaseHas('tap_zones', $attributes);
        $tap_zone = TapZone::where($attributes)->first();

        $location = [
            'latitude' => 14.618562,
            'longitude' => 121.052468,
        ];

        $channel_id = $this->faker->randomNumber(8);
        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives("/checkin")
            ->assertReply(trans('checkin.introduction'))
            ->assertTemplate(OutgoingMessage::class)
            ->receivesLocation($location['latitude'], $location['longitude'])
            ->assertTemplate(Question::class)
            ->receives("signup")
            ->assertReply(trans('checkin.processing'))
            ->assertReply(trans('checkin.processed'))
            ->assertReplyNothing()
            ;

        $user = User::where(compact('driver', 'channel_id'))->first();

        $attributes = array_merge(['user_id' => $user->id, 'remarks' => 'signup'], $location);
        $this->assertDatabaseHas('checkins', $attributes);

        $checkin = Checkin::where($attributes)->first();
        $this->assertEquals($checkin->user->parent->id, $admin->id);

        \Queue::assertPushed(\App\Jobs\ReverseGeocode::class);
    }
}
