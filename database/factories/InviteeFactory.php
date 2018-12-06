<?php

use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;

$factory->define(App\Invitee::class, function (Faker $faker) {

    $faker = FakerFactory::create('en_PH');

    return [
        'mobile' => $faker->phoneNumber,
        'role' => array_random(['staff','worker','subscriber']),
        'user_id' => 1,
        'message' => $faker->sentence,
    ];
});
