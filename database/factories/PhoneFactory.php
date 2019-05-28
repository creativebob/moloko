<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Phone;
use Faker\Generator as Faker;

$factory->define(Phone::class, function (Faker $faker) {
    return [
       'phone' => $phone = $faker->unique()->regexify("89[0-9]{9}"),
       'crop' => substr(cleanPhone($phone), -4),
    ];
});
