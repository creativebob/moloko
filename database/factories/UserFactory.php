<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use App\Company;
use App\Phone;

use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/


$companies_count = Company::count();

$factory->define(User::class, function (Faker $faker) use ($companies_count) {





	
    return [
        'login' => $faker->userName,

        'company_id' => rand(1, $companies_count),

        'first_name' => $faker->firstName,
        'second_name' => $faker->lastName,

        // 'first_name' => $faker->randomElement(array('Андрей', 'Илья', 'Сергей', 'Иван', 'Евгений', 'Александр', 'Николай', 'Василий', 'Роман', 'Юрий', 'Степан', 'Виктор')),

        // 'second_name' => $faker->randomElement(array('Иванов', 'Петров', 'Сидоров', 'Хворин', 'Гудков', 'Гербудов', 'Веллер', 'Хаджикулов', 'Суриков', 'Чичваркин', 'Алибасов', 'Нордников', 'Лермонтов')),

        'patronymic' => $faker->firstName,
        'nickname' => $faker->userName,
        'sex' => $faker->boolean,
        // 'birthday' => $faker->date($format = 'Y-m-d', $max = 'now'),

        'user_inn' => $faker->unique()->regexify("[0-9]{12}"),
        'passport_address' => $faker->address,
        'passport_number' => $faker->unique()->regexify("[0-9]{10}"),
        'passport_released' => $faker->word(5),
        // 'passport_date' => $faker->date($format = 'Y-m-d', $max = 'now'),

        'about' => $faker->paragraph(),
        'specialty' => $faker->word(10),
        'degree' => $faker->word(8),
        'quote' => $faker->text(5),

        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),

        'user_type' => $faker->numberBetween($min = 0, $max = 1),
        'access_block' => $faker->numberBetween($min = 0, $max = 1),

		// $number = $faker->numberBetween($min = 11, $max = 11);

    ];
});

$factory->afterCreating(User::class, function ($user, Faker $faker) {

	$phone = factory(Phone::class)->create();
	$user->phones()->attach($phone->id, ['main' => 1]);

});