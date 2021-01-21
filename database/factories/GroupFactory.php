<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserGroup;
use Faker\Generator as Faker;

$factory->define(App\UserGroup::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'created_by' => 1,
        'created_at' => now(),
    ];
});

