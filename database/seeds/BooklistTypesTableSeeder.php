<?php

use Illuminate\Database\Seeder;

class BooklistTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('booklist_types')->insert([
        	[
		        'name' => 'Простой список',
                'tag' => 'simple',
                'entity_alias' => null,
		        'description' => 'Обычный список для текущей работы каждого пользователя',
                'change_allowed' => 1,
		        'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
        	],
            [
                'name' => 'Территориии спроса',
                'tag' => 'marketing',
                'entity_alias' => 'cities',
                'description' => 'Группа городов которая используется для сбора статистики по запросам в поисковых системах и не только',
                'change_allowed' => 0,
                'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Список авторов для доступа',
                'tag' => 'rights',
                'entity_alias' => 'users',
                'description' => 'Список авторов который можно использовать для расширения прав другого пользователя',
                'change_allowed' => 1,
                'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
            ],
        ]);
    }
}