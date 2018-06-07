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
        	['name' => 'Просмотр', 'method' => 'view'],
        	['name' => 'Просмотр списка', 'method' => 'index'],
        	['name' => 'Добавление', 'method' => 'create'],
        	['name' => 'Редактирование', 'method' => 'update'],
        	['name' => 'Удаление', 'method' => 'delete'],
        	['name' => 'Системная', 'method' => 'system'],
            ['name' => 'Другие авторы', 'method' => 'authors'],
        	['name' => 'Нет ограничений', 'method' => 'nolimit'],
            ['name' => 'Автомодерация', 'method' => 'automoderate'],
            ['name' => 'Модератор', 'method' => 'moderator'],
            ['name' => 'Отображение на сайте', 'method' => 'publisher'],
        ]);
    }
}
