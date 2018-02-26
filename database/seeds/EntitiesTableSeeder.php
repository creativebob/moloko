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
                'entity_model' => 'User',
                'system_item' => 1,
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Компании',
		        'entity_alias' => 'companies',
                'entity_model' => 'Company',
                'system_item' => 1,
                'author_id' => 1,
        	],
            [
                'entity_name' => 'Штат',
                'entity_alias' => 'staff',
                'entity_model' => 'Staffer',
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Отделы',
                'entity_alias' => 'departments',
                'entity_model' => 'Department',
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Должности',
                'entity_alias' => 'positions',
                'entity_model' => 'Position',
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Сотрудники',
                'entity_alias' => 'employees',
                'entity_model' => 'Employee',
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Области',
                'entity_alias' => 'regions',
                'entity_model' => 'Region',
                'system_item' => 1,
                'author_id' => 1,
            ],
        	[
		        'entity_name' => 'Районы',
		        'entity_alias' => 'areas',
                'entity_model' => 'Area',
                'system_item' => 1,
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Населенные пункты',
		        'entity_alias' => 'cities',
                'entity_model' => 'City',
                'system_item' => 1,
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Категории правил',
		        'entity_alias' => 'category_right',
                'entity_model' => 'Category_right',
                'system_item' => 1,
                'author_id' => 1,
        	],
        	[
		        'entity_name' => 'Сущности',
		        'entity_alias' => 'entities',
                'entity_model' => 'Entity',
                'system_item' => 1,
                'author_id' => 1,
        	],
            [
                'entity_name' => 'Сайты',
                'entity_alias' => 'sites',
                'entity_model' => 'Site',
                'system_item' => 1,
                'author_id' => 1,
            ],
        	[
		        'entity_name' => 'Страницы',
		        'entity_alias' => 'pages',
                'entity_model' => 'Page',
                'system_item' => 1,
                'author_id' => 1,
        	],
            [
                'entity_name' => 'Навигации',
                'entity_alias' => 'navigations',
                'entity_model' => 'Navigation',
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Меню',
                'entity_alias' => 'menus',
                'entity_model' => 'Menu',
                'system_item' => 1,
                'author_id' => 1,
            ],
        	[
		        'entity_name' => 'Правила',
		        'entity_alias' => 'rights',
                'entity_model' => 'Right',
                'system_item' => 1,
                'author_id' => 1,
        	],
            [
                'entity_name' => 'Роли',
                'entity_alias' => 'roles',
                'entity_model' => 'Role',
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'entity_name' => 'Списки',
                'entity_alias' => 'booklists',
                'entity_model' => 'Booklist',
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
