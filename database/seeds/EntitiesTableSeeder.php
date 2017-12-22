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
        	],
        	[
		        'entity_name' => 'Компании',
		        'entity_alias' => 'company',
        	],
        	[
		        'entity_name' => 'Отделы',
		        'entity_alias' => 'departments',
        	],
        	[
		        'entity_name' => 'Доступ',
		        'entity_alias' => 'accesses',
        	],
        	[
		        'entity_name' => 'Группы доступа',
		        'entity_alias' => 'access_groups',
        	],
        	[
		        'entity_name' => 'Районы',
		        'entity_alias' => 'areas',
        	],
        	[
		        'entity_name' => 'Регионы',
		        'entity_alias' => 'regions',
        	],
        	[
		        'entity_name' => 'Города',
		        'entity_alias' => 'cities',
        	],
        	[
		        'entity_name' => 'Категории правил',
		        'entity_alias' => 'category_right',
        	],
        	[
		        'entity_name' => 'Сущности',
		        'entity_alias' => 'entities',
        	],
        	[
		        'entity_name' => 'Страницы',
		        'entity_alias' => 'pages',
        	],
        	[
		        'entity_name' => 'Должности',
		        'entity_alias' => 'positions',
        	],
        	[
		        'entity_name' => 'Правила',
		        'entity_alias' => 'rights',
        	],
        	[
		        'entity_name' => 'Сайты',
		        'entity_alias' => 'sites',
        	],
        	[
		        'entity_name' => 'Сотрудники',
		        'entity_alias' => 'employees',
        	],
        ]);
    }
}
