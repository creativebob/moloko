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
	        	'role_id' => 1, 
                'department_id' => null, 
	        	'user_id' => 1, 
                'position_id' => null, 
        	],
        	[
	        	'role_id' => 1, 
                'department_id' => null, 
	        	'user_id' => 2, 
                'position_id' => null, 
        	],
        	[
	        	'role_id' => 1, 
                'department_id' => null, 
	        	'user_id' => 3, 
                'position_id' => null, 
        	],
        ]);

    }
}
