<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use App\Company;
use App\Phone;
use App\FirstName;
use App\Surname;
// use Carbon\Carbon;

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

// Получаем список справочных имен и отчеств
$first_names = FirstName::get();

// Получаем список справочных фамилий
$surnames = Surname::get();

$factory->define(User::class, function (Faker $faker) use ($companies_count, $first_names, $surnames) {

    $first_names_male = $first_names->where('gender', 1);
    $first_names_female = $first_names->where('gender', 0);

    $first_names_random = $first_names->random();


    if($first_names_random->gender == 1){

        $first_name = $first_names_male->random()->name;
        $patronymic = $first_names_male->random()->patronymic_male;
        $second_name = $surnames->random()->surname_male;

    } else {

        $first_name = $first_names_female->random()->name;
        $patronymic = $first_names_male->random()->patronymic_female;
        $second_name = $surnames->random()->surname_female;

    };


    return [
        'login' => $faker->unique()->userName,
        'company_id' => rand(1, $companies_count),

        'first_name' => $first_name,
        'second_name' => $second_name,
        'patronymic' => $patronymic,

        'nickname' => $faker->userName,
        'sex' => $first_names_random->gender,
        'birthday_date' => $faker->dateTimeBetween('-80 years', '-18 years')->format('d.m.Y'),

        'user_inn' => $faker->unique()->regexify("[0-9]{12}"),
        'passport_address' => $faker->address,
        'passport_number' => $faker->unique()->regexify("[0-9]{10}"),

        'passport_released' => $faker->randomElement($array = array('РОВД', 'ОМ-2', 'ОМ-1', 'МВД', 'Районный отдел внутренних дел')) . ' ' . $faker->randomElement($array = array('города ', 'г. ')) . $faker->randomElement($array = array('Урюпинска', 'Орел', 'Иркутска', 'Загорска')),

        'passport_date' => $faker->dateTimeBetween('-80 years', 'now')->format('d.m.Y'),

        'about' => $faker->paragraph(),
        'specialty' => $faker->randomElement(array('Экономист', 'Стоматолог', 'Учитель русского языка', 'Врач травматолог', 'Продавец', 'Нарколог', 'Инжерер', 'Механик', 'Водитель', 'Воспитатель', 'Юрист', 'Сантехник', 'Эколог', 'Уфолог', 'Главный специалист', 'Ветеринар', 'Администратор', 'Бармен')),
        'degree' => $faker->randomElement(array('Специаист', 'Доктор', 'Профессор', null)),
        'quote' => $faker->text(30),

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
