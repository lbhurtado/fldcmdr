<?php

use Faker\Generator as Faker;

$factory->define(App\AirTime::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'amount' => $faker->randomFloat(2, 10, 500)
    ];
});
