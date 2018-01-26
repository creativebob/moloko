<?php

use Illuminate\Database\Seeder;

class EntitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('entities')->insert([
        	[
		        'entity_name' => 'Пользователи',
		        'entity_alias' => 'users',
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Компании',
		        'entity_alias' => 'companies',
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Отделы',
		        'entity_alias' => 'departments',
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Районы',
		        'entity_alias' => 'areas',
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Регионы',
		        'entity_alias' => 'regions',
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Города',
		        'entity_alias' => 'cities',
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Категории правил',
		        'entity_alias' => 'category_right',
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Сущности',
		        'entity_alias' => 'entities',
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Страницы',
		        'entity_alias' => 'pages',
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Должности',
		        'entity_alias' => 'positions',
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Правила',
		        'entity_alias' => 'rights',
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Сайты',
		        'entity_alias' => 'sites',
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Сотрудники',
		        'entity_alias' => 'employees',
                'author_id' => 1,
        	],
            [
                'entity_name' => 'Меню',
                'entity_alias' => 'menus',
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Роли',
                'entity_alias' => 'roles',
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Сайты',
                'entity_alias' => 'sites',
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Списки',
                'entity_alias' => 'booklists',
                'author_id' => 1,
            ],



            // Связующие сущности

            // [
            //     'entity_name' => 'Связь Право-Роль',
            //     'entity_alias' => 'right_role',
            //     'author_id' => 1,
            // ],
            // [
            //     'entity_name' => 'Связь Роль-Пользователь',
            //     'entity_alias' => 'role_user',
            //     'author_id' => 1,
            // ],
            // [
            //     'entity_name' => 'Связь Действие-Сущность',
            //     'entity_alias' => 'action_entity',
            //     'author_id' => 1,
            // ],
            
        ]);
    }
}
