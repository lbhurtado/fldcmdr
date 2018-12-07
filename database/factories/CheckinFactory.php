<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(App\Checkin::class, function (Faker $faker) {
    return [
        'longitude' => $faker->longitude,
        'latitude' => $faker->latitude,
        // 'user_id' => factory(User::class)->create()->id,
        'remarks' => $faker->sentence,
    ];
});
