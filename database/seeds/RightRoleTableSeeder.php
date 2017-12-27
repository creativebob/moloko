<?php

use Illuminate\Database\Seeder;

class RightRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('right_role')->insert([
        	['right_id' => 1, 'role_id' => 1], 
        	['right_id' => 2, 'role_id' => 1], 
        	['right_id' => 3, 'role_id' => 1], 
        	['right_id' => 4, 'role_id' => 1], 
            ['right_id' => 5, 'role_id' => 1], 
            ['right_id' => 6, 'role_id' => 1], 
            ['right_id' => 7, 'role_id' => 1], 

            ['right_id' => 8, 'role_id' => 1], 
            ['right_id' => 9, 'role_id' => 1], 
            ['right_id' => 10, 'role_id' => 1], 

        ]);
    }
}