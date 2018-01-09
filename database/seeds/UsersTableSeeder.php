<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
        	[
	        	'login' => 'robot', 
	        	'email' => 'robocop@mail.ru', 
	        	'password' => bcrypt('111111'), 
	        	'nickname' => 'robot', 
	        	'first_name' => 'Super',
	        	'second_name' => 'User',
	        	'phone' => '89000000000', 
	        	'user_type' => 1, 
	        	'access_block' => 0, 
	        	'company_id' => Null, 
	        	'god' => 1, 
        	],
        	[
	        	'login' => 'creativebob', 
	        	'email' => 'creativebob@mail.ru', 
	        	'password' => bcrypt('111111'), 
	        	'nickname' => 'creativebob', 
	        	'first_name' => 'Алексей',
	        	'second_name' => 'Солтысяк',
	        	'phone' => '89041248598', 
	        	'user_type' => 1, 
	        	'access_block' => 0, 
	        	'company_id' => Null, 
	        	'god' => 1, 
        	],
        	[
	        	'login' => 'makc_berluskone', 
	        	'email' => 'makc_berluskone@mail.ru', 
	        	'password' => bcrypt('123456'), 
	        	'first_name' => 'Максон',
	        	'second_name' => 'Великий',
	        	'nickname' => 'Makc_Berluskone', 
	        	'phone' => '88888888888', 
	        	'user_type' => 1, 
	        	'access_block' => 0, 
	        	'company_id' => Null, 
	        	'god' => 1, 
        	],
        	[
	        	'login' => 'timoshenko', 
	        	'email' => 'sidorov@mail.ru', 
	        	'password' => bcrypt('123456'), 
	        	'nickname' => 'timoshenko', 
	        	'first_name' => 'Алексей',
	        	'second_name' => 'Тимошенко',
	        	'phone' => '89024673334', 
	        	'user_type' => 1, 
	        	'access_block' => 0, 
	        	'company_id' => 1, 
	        	'god' => null, 
        	],
        	[
	        	'login' => 'mironov', 
	        	'email' => 'pacanhule@mail.ru', 
	        	'password' => bcrypt('123456'), 
	        	'nickname' => 'mironov', 
	        	'first_name' => 'Юрий',
	        	'second_name' => 'Миронов',
	        	'phone' => '89024112734', 
	        	'user_type' => 1, 
	        	'access_block' => 0, 
	        	'company_id' => 1, 
	        	'god' => null, 
        	],
        	[
	        	'login' => 'kondrachuk', 
	        	'email' => 'ivanov@mail.ru', 
	        	'password' => bcrypt('123456'), 
	        	'nickname' => 'kondrachuk', 
	        	'first_name' => 'Анна',
	        	'second_name' => 'Кондрачук',
	        	'phone' => '86784672734', 
	        	'user_type' => 1, 
	        	'access_block' => 0, 
	        	'company_id' => 1, 
	        	'god' => null, 
        	],
        	[
	        	'login' => 'davydenko', 
	        	'email' => 'd@vorotamars.ru', 
	        	'password' => bcrypt('123456'), 
	        	'first_name' => 'Максим',
	        	'second_name' => 'Давыденко',
	        	'nickname' => 'davydenko', 
	        	'phone' => '89025687585', 
	        	'user_type' => 1, 
	        	'access_block' => 0, 
	        	'company_id' => 1, 
	        	'god' => null, 
        	],
        ]);

    }
}
