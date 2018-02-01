<?php

use Illuminate\Database\Seeder;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('role_user')->insert([
        	[
	        	'role_id' => '1', 
                'department_id' => '1', 
	        	'user_id' => '1', 
                'position_id' => null, 
        	],
        	[
	        	'role_id' => '1', 
                'department_id' => '1', 
	        	'user_id' => '2', 
                'position_id' => null, 
        	],
        	[
	        	'role_id' => '1', 
                'department_id' => '1', 
	        	'user_id' => '3', 
                'position_id' => null, 
        	],
            [
                'role_id' => '3', 
                'department_id' => '1', 
                'user_id' => '4', 
                'position_id' => 1, 
            ],
            [
                'role_id' => '4', 
                'department_id' => '1',   
                'user_id' => '5', 
                'position_id' => 2, 
            ],
            [
                'role_id' => '5', 
                'department_id' => '1', 
                'user_id' => '6', 
                'position_id' => 3, 
            ],
            [
                'role_id' => '1', 
                'department_id' => '1', 
                'user_id' => '4', 
                'position_id' => null, 
            ],
        ]);

    }
}
