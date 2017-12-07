<?php

use Illuminate\Database\Seeder;

class RightsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rights')->insert([
        	['right_name' => 'Добавление пользователя', 'right_action' => 'create-user', 'category_right_id' => 1], 
        	['right_name' => 'Редактирование пользователя', 'right_action' => 'update-user', 'category_right_id' => 1], 
        	['right_name' => 'Просмотр пользователя', 'right_action' => 'view-user', 'category_right_id' => 1], 
        	['right_name' => 'Удаление пользователя', 'right_action' => 'delete-user', 'category_right_id' => 1], 	
            ['right_name' => 'Просмотр списка пользователей', 'right_action' => 'index-user', 'category_right_id' => 1], 
        ]);
    }
}
