<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(App\Tag::class, function (Faker $faker) {
    return [
        'code' => $faker->word,
        'tagger_id' => factory(User::class)->create()->id,
        'tagger_type' => User::class,
    ];
});
