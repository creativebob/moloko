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
        	['role_name' => 'Полный доступ', 'company_id' => null, 'system_item' => 1], 
        	['role_name' => 'Администратор', 'company_id' => null, 'system_item' => 1], 
            ['role_name' => 'Руководитель', 'company_id' => null, 'system_item' => 1],  
            ['role_name' => 'Менеджер', 'company_id' => null, 'system_item' => 1], 
            ['role_name' => 'Все филиалы', 'company_id' => 1, 'system_item' => null],  
            ['role_name' => 'Иркутский филиал', 'company_id' => 1, 'system_item' => null], 
        ]);
    }
}
