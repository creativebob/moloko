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
	        	'first_name' => 'Максим',
	        	'second_name' => 'Давыденко',
	        	'nickname' => 'Makc_Berluskone', 
	        	'phone' => '89025687585', 
	        	'user_type' => 1, 
	        	'access_block' => 0, 
	        	'company_id' => Null, 
	        	'god' => 1, 
        	],
        	[
	        	'login' => 'usertest', 
	        	'email' => 'pacanhule@mail.ru', 
	        	'password' => bcrypt('123456'), 
	        	'nickname' => 'nikola', 
	        	'first_name' => 'Лол',
	        	'second_name' => 'Первый',
	        	'phone' => '89024112734', 
	        	'user_type' => 1, 
	        	'access_block' => 0, 
	        	'company_id' => 1, 
	        	'god' => null, 
        	],
        	[
	        	'login' => 'ivanov', 
	        	'email' => 'ivanov@mail.ru', 
	        	'password' => bcrypt('123456'), 
	        	'nickname' => 'ivanov', 
	        	'first_name' => 'Лол',
	        	'second_name' => 'Второй',
	        	'phone' => '86784672734', 
	        	'user_type' => 1, 
	        	'access_block' => 0, 
	        	'company_id' => 1, 
	        	'god' => null, 
        	],
        	[
	        	'login' => 'sidorov', 
	        	'email' => 'sidorov@mail.ru', 
	        	'password' => bcrypt('123456'), 
	        	'nickname' => 'sidorov', 
	        	'first_name' => 'Лол',
	        	'second_name' => 'Третий',
	        	'phone' => '89024673334', 
	        	'user_type' => 1, 
	        	'access_block' => 0, 
	        	'company_id' => 1, 
	        	'god' => null, 
        	],
        ]);

    }
}
