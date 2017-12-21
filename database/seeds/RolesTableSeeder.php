<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
        	['role_name' => 'Полный доступ', 'category_right_id' => 1], 
        	['role_name' => 'Администратор', 'category_right_id' => 1], 
            ['role_name' => 'Руководитель', 'category_right_id' => 1], 
            ['role_name' => 'Менеджер', 'category_right_id' => 1], 
            ['role_name' => 'Все филиалы', 'category_right_id' => 2], 
            ['role_name' => 'Иркутский филиал', 'category_right_id' => 2], 
        ]);
    }
}
