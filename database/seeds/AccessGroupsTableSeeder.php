<?php

use Illuminate\Database\Seeder;

class AccessGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('access_groups')->insert([
        	['access_group_name' => 'Бог функционала', 'category_right_id' => 1], 
        	['access_group_name' => 'Бог филиалов', 'category_right_id' => 2], 
        ]);
    }
}
