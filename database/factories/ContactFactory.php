<?php

use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;

$factory->define(App\Contact::class, function (Faker $faker) {
    $faker = FakerFactory::create('en_PH');

    return [
    	'mobile' => $faker->phoneNumber,
    ];
});
