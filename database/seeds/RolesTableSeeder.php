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
            ['role_name' => 'Модератор новостей сайта', 'company_id' => null, 'system_item' => 1],  
            ['role_name' => 'Смотрящий на районе', 'company_id' => null, 'system_item' => 1], 
            ['role_name' => 'Бесправное чмо', 'company_id' => null, 'system_item' => 1],  
        ]);
    }
}
