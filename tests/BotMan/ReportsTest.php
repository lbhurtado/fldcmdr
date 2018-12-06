<?php

namespace Tests\BotMan;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Exports\RolesExport;
use Maatwebsite\Excel\Facades\Excel;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = "/reports";

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');
    }

    /** @test */
    public function reports_success_run()
    {
        Excel::fake();

        $driver = 'Telegram';
        $channel_id = $this->faker->randomNumber(8);
        $message = $this->faker->sentence;

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('chatbot.list.reports'))
            ;
    }
}
