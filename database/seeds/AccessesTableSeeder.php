<?php

use Illuminate\Database\Seeder;

class AccessesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accesses')->insert([
        	['right_action' => 'create-user', 'access_group_id' => 1], 
        	['right_action' => 'update-user', 'access_group_id' => 1], 
        	['right_action' => 'view-user', 'access_group_id' => 1], 
        	['right_action' => 'delete-user', 'access_group_id' => 1], 
        ]);
    }
}
