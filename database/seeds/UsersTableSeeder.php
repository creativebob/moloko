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
                'phone' => 89000000000, 
                'location_id' => 1,
                'user_type' => 1, 
                'access_block' => 0, 
                'company_id' => null, 
                'filial_id' => null,
                'god' => null, 
                'system_item' => 1, 
                'author_id' => null, 
                'moderation' => null,
                'sex' => 1, 
            ],
            [
                'login' => 'creativebob', 
                'email' => 'creativebob@mail.ru', 
                'password' => bcrypt('111111'), 
                'nickname' => 'creativebob', 
                'first_name' => 'Nestor',
                'second_name' => 'Господин',
                'phone' => 89041248598, 
                'location_id' => 1,
                'user_type' => 1, 
                'access_block' => 0, 
                'company_id' => null, 
                'filial_id' => null,
                'god' => 1, 
                'system_item' => null, 
                'author_id' => 1,
                'moderation' => null,
                'sex' => 1, 
            ],
            [
               'login' => 'makc_berluskone', 
               'email' => 'makc_berluskone@mail.ru', 
               'password' => bcrypt('123456'), 
               'nickname' => 'Makc_Berluskone', 
               'first_name' => 'Максон',
               'second_name' => 'Великий',
               'phone' => 88888888888, 
               'location_id' => 1,
               'user_type' => 1, 
               'access_block' => 0, 
               'company_id' => null,
               'filial_id' => null, 
               'god' => 1, 
               'system_item' => null, 
               'author_id' => 1, 
               'moderation' => null, 
               'sex' => 1, 
           ],

       ]);
    }
}
