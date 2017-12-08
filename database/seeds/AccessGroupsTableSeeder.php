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
        	['access_group_name' => 'Полный доступ', 'category_right_id' => 1], 
        	['access_group_name' => 'Администратор', 'category_right_id' => 1], 
            ['access_group_name' => 'Руководитель', 'category_right_id' => 1], 
            ['access_group_name' => 'Менеджер', 'category_right_id' => 1], 
            ['access_group_name' => 'Все филиалы', 'category_right_id' => 2], 
            ['access_group_name' => 'Иркутский филиал', 'category_right_id' => 2], 
        ]);
    }
}
