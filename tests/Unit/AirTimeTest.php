<?php

namespace Tests\Unit;

use App\AirTime;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AirTimeTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	/** @test */
    public function airtime_has_name_default_amount_zero()
    {
    	$name = $this->faker->name;
    	$airtime = AirTime::create(compact('name'));

        $this->assertEquals($airtime->name, $name);
        $this->assertDatabaseHas('air_times', [
        	'name' => $name,
        	'amount' => 0.00,
        ]);
    }

	/** @test */
    public function airtime_has_amount()
    {
    	$name = $this->faker->name;
    	$amount = $this->faker->randomFloat(2, 10, 500);
    	$airtime = AirTime::create(compact('name', 'amount'));

        $this->assertEquals($airtime->name, $name);
        $this->assertEquals($airtime->amount, $amount);
        $this->assertDatabaseHas('air_times', [
        	'name' => $name,
        	'amount' => $amount,
        ]);
    }
}
