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
        	['role_name' => 'Полный доступ', 'company_id' => null, 'system_item' => null, 'author_id' => 1], 
        	['role_name' => 'Администратор', 'company_id' => null, 'system_item' => null, 'author_id' => 1], 
            ['role_name' => 'Директор', 'company_id' => null, 'system_item' => null, 'author_id' => 1], 
            ['role_name' => 'Менеджер', 'company_id' => null, 'system_item' => null, 'author_id' => 1], 
            ['role_name' => 'Web-разработчик', 'company_id' => 1, 'system_item' => null, 'author_id' => 4],
            ['role_name' => 'Сторож', 'company_id' => 2, 'system_item' => null, 'author_id' => 14],  
        ]);
    }
}
