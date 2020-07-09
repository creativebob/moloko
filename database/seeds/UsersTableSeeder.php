<?php

use Illuminate\Database\Seeder;

use App\User;
use App\Phone;

class UsersTableSeeder extends Seeder
{

    public function run()
    {

    	DB::table('users')->insert([
    		[
                'login' => 'robot',
                'email' => 'robocop@mail.ru',
                'password' => bcrypt('mbcb13'),
                'nickname' => 'robot',
                'first_name' => 'Чат',
                'second_name' => 'Бот',
                // 'phone' => 89000000000,
                'location_id' => null,
                'user_type' => 1,
                'access_block' => 0,
                'company_id' => null,
                'filial_id' => null,
                'god' => null,
                'system' => true,
                'author_id' => null,
                'gender' => 1,
            ],
            [
                'login' => 'creativebob',
                'email' => 'creativebob@mail.ru',
                'password' => bcrypt('111111'),
                'nickname' => 'creativebob',
                'first_name' => 'Nestor',
                'second_name' => 'Господин',
                // 'phone' => 89041248598,
                'location_id' => null,
                'user_type' => 1,
                'access_block' => 0,
                'company_id' => null,
                'filial_id' => null,
                'god' => 1,
                'system' => false,
                'author_id' => 1,
                'gender' => 1,
            ],
            [
                'login' => 'makc_berluskone',
                'email' => 'makc_berluskone@mail.ru',
                'password' => bcrypt('123456'),
                'nickname' => 'Makc_Berluskone',
                'first_name' => 'Максон',
                'second_name' => 'Великий',
                // 'phone' => 88888888888,
                'location_id' => null,
                'user_type' => 1,
                'access_block' => 0,
                'company_id' => null,
                'filial_id' => null,
                'god' => 1,
                'system' => false,
                'author_id' => 1,
                'gender' => 1,
            ],
        ]);

        $users = User::get();

        // Пишем или ищем новый и создаем связь
        $phone = Phone::firstOrCreate(
            ['phone' => 88888888888],
            ['crop' => substr(88888888888, -4)]
        );
        // dd($phone);

        foreach ($users as $user) {
            $user->phones()->attach($phone->id, ['main' => 1]);
        }
    }
}
