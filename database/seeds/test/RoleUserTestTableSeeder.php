<?php

use Illuminate\Database\Seeder;

class RoleUserTestTableSeeder extends Seeder
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
	        	'role_id' => 2, 
                'department_id' => 1, 
	        	'user_id' => 4, 
                'position_id' => null, 
        	],
        	[
	        	'role_id' => 3, 
                'department_id' => 2, 
	        	'user_id' => 5, 
                'position_id' => null, 
        	],
            [
                'role_id' => 3, 
                'department_id' => 2, 
                'user_id' => 6, 
                'position_id' => null, 
            ],
        ]);

    }
}
