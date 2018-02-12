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
                'system_item' => 1,
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Компании',
		        'entity_alias' => 'companies',
                'system_item' => 1,
                'author_id' => 1,
        	],
            [
                'entity_name' => 'Штат',
                'entity_alias' => 'staff',
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Отделы',
                'entity_alias' => 'departments',
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Должности',
                'entity_alias' => 'positions',
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Сотрудники',
                'entity_alias' => 'employees',
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Области',
                'entity_alias' => 'regions',
                'system_item' => 1,
                'author_id' => 1,
            ],
        	[
		        'entity_name' => 'Районы',
		        'entity_alias' => 'areas',
                'system_item' => 1,
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Населенные пункты',
		        'entity_alias' => 'cities',
                'system_item' => 1,
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Категории правил',
		        'entity_alias' => 'category_right',
                'system_item' => 1,
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Сущности',
		        'entity_alias' => 'entities',
                'system_item' => 1,
                'author_id' => 1,
        	],
            [
                'entity_name' => 'Сайты',
                'entity_alias' => 'sites',
                'system_item' => 1,
                'author_id' => 1,
            ],
        	[
		        'entity_name' => 'Страницы',
		        'entity_alias' => 'pages',
                'system_item' => 1,
                'author_id' => 1,
        	],
            [
                'entity_name' => 'Навигации',
                'entity_alias' => 'navigations',
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Меню',
                'entity_alias' => 'menus',
                'system_item' => 1,
                'author_id' => 1,
            ],
        	[
		        'entity_name' => 'Правила',
		        'entity_alias' => 'rights',
                'system_item' => 1,
                'author_id' => 1,
        	],
            [
                'entity_name' => 'Роли',
                'entity_alias' => 'roles',
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Списки',
                'entity_alias' => 'booklists',
                'system_item' => 1,
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
