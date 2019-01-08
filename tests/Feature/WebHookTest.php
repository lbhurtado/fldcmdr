<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Eloquent\Phone;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebHookTest extends TestCase
{
	use RefreshDatabase, WithFaker;

    private $secret = 'test';

    private $from;

    private $to;

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');

        $this->from = $this->faker->mobileNumber;
        $this->to = $this->faker->mobileNumber;
    }

	/** @test */
    public function server_webhook_accepts_sms_get()
    {
        $query = http_build_query($this->getData());

        $response = $this->get('/webhook/sms?' . $query);
        $response->assertStatus(200);
    }

    /** @test */
    public function server_webhook_accepts_sms_post()
    {
        $query = http_build_query($this->getData());

        $response = $this->post('/webhook/sms?' . $query);
        $response->assertStatus(200);
    }

    /** @test */
    public function server_webhook_does_not_accept_sms_without_secret()
    {
        $invalid_secret = $this->faker->sentence;

        $query = http_build_query(['secret' => $invalid_secret]);

        $response = $this->post('/webhook/sms?' . $query);
        $response->assertStatus(302);
    }

    /** @test */
    public function server_webhook_persists_message()
    {
        $message = $this->faker->word;
        $data = $this->getData(compact('message'));
        $query = http_build_query($data);

        $this->post('/webhook/sms?' . $query);

        array_pull($data, 'secret');
        $this->assertDatabaseHas('s_m_s_s', $data);
    }

    protected function getData($array = [])
    {
        $data = [
            'secret' => $this->secret,
            'message' => $this->faker->paragraph,
            'from' => Phone::number($this->from),
            'to' => Phone::number($this->to),
        ];

        return array_merge($data, $array);
    }
}
