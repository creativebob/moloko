<?php

use Illuminate\Database\Seeder;

class PositionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('position_role')->insert([
        	['position_id' => 1, 'role_id' => 3], 
        	['position_id' => 2, 'role_id' => 4], 


        ]);
    }
}
