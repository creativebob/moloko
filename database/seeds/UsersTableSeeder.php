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
	        	'login' => 'creativebob', 
	        	'email' => 'creativebob@mail.ru', 
	        	'password' => bcrypt('111111'), 
	        	'nickname' => 'creativebob', 
	        	'phone' => '89041248598', 
	        	'group_action_id' => 1, 
	        	'group_locality_id' => 5, 
	        	'contragent_status' => 1, 
	        	'access_block' => 0, 
        	],
        	[
	        	'login' => 'makc_berluskone', 
	        	'email' => 'makc_berluskone@mail.ru', 
	        	'password' => bcrypt('123456'), 
	        	'nickname' => 'Makc_Berluskone', 
	        	'phone' => '89025687585', 
	        	'group_action_id' => 1, 
	        	'group_locality_id' => 5, 
	        	'contragent_status' => 1, 
	        	'access_block' => 0, 
        	],
        ]);

    }
}
