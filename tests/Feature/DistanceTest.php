<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\{User, Checkin, TapZone};
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DistanceTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	/** @test */
	public function checkin_distance_can_be_measured()
	{
		$user = factory(User::class)->create();
		//West Maya Drive, Philam Homes
		$longitude = 121.028884;
		$latitude = 14.646914;
		$checkin = Checkin::make(compact('longitude', 'latitude'));
		$checkin->user()->associate($user);
		$checkin->save();

		//Farmer's Plaza
		$lon = 121.052468;
		$lat = 14.618562;

		$distance = $checkin->distance(14.618562, 121.052468)->first()->distance;
		$this->assertTrue($distance > 4);
		$this->assertTrue($distance < 5);	
	}

	/** @test */
	public function tapzone_distance_can_be_measured()
	{
		$user = factory(User::class)->create();
		//Metro Manila
		$longitude = 120.984222;
		$latitude = 14.599512;
		$tapzone = TapZone::make(compact('longitude', 'latitude'));
		$tapzone->user()->associate($user);
		$tapzone->save();

		//Taytay Rizal
		$lon = 121.136086;
		$lat = 14.558555;

		$distance = $tapzone->distance($lat, $lon)->first()->distance;

		$this->assertTrue($distance > 10);	
	}
}
