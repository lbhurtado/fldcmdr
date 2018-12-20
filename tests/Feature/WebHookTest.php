<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebHookTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
    public function server_accepts_sms_webhook()
    {
    	$data = [
    		'secret' => 'test',
    		'message' => 'Applester',
    		'from' => '639393038503',
    		'to' => '639081877788'
		];

        $response = $this->get('/webhook/sms?' . http_build_query($data));

        $response->assertStatus(200);
    }
}
