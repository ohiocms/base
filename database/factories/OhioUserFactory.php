<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(Ohio\Core\Model\User\Domain\User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'mi' => $faker->randomLetter,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        //'remember_token' => str_random(10),
    ];
});
