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
        	['name' => 'Полный доступ', 'company_id' => null, 'system_item' => null, 'author_id' => 1], 
        	['name' => 'Администратор', 'company_id' => null, 'system_item' => null, 'author_id' => 1], 
            ['name' => 'Директор', 'company_id' => null, 'system_item' => null, 'author_id' => 1], 
            ['name' => 'Менеджер', 'company_id' => null, 'system_item' => null, 'author_id' => 1], 
            ['name' => 'Web-разработчик', 'company_id' => 1, 'system_item' => null, 'author_id' => 4],
            ['name' => 'Сторож', 'company_id' => 2, 'system_item' => null, 'author_id' => 14],  
        ]);
    }
}
