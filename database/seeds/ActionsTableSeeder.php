<?php

use Illuminate\Database\Seeder;

class ActionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('actions')->insert([
        	['action_name' => 'Просмотр', 'action_method' => 'view'], 
        	['action_name' => 'Просмотр списка', 'action_method' => 'index'], 
        	['action_name' => 'Добавление', 'action_method' => 'create'], 
        	['action_name' => 'Редактирование', 'action_method' => 'edit'], 
        	['action_name' => 'Удаление', 'action_method' => 'delete'], 
        	['action_name' => 'Системная', 'action_method' => 'system'], 
            ['action_name' => 'Другие авторы', 'action_method' => 'authors'], 
        	['action_name' => 'Нет ограничений', 'action_method' => 'nolimit'], 
            ['action_name' => 'Модерация', 'action_method' => 'moderation'], 
        ]);
    }
}
