<?php

use Illuminate\Database\Seeder;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Создаем страницы для crm системы
		 DB::table('pages')->insert([
            [
                'page_name' => 'Компании',
                'site_id' => '1',
                'page_title' => 'Компании',
                'page_description' => 'Компании в системе автоматизации',
                'page_alias' => '/companies',
                'company_id' => null,
                'system_item' => 1,
            ],
            [
                'page_name' => 'Пользователи',
                'site_id' => '1',
                'page_title' => 'Пользователи системы',
                'page_description' => 'Пользователи системы',
                'page_alias' => '/users',
                'company_id' => null,
                'system_item' => 1,
            ],
            [
                'page_name' => 'Филиалы и отделы',
                'site_id' => '1',
                'page_title' => 'Филиалы и отделы',
                'page_description' => 'Филиалы и отделы',
                'page_alias' => '/departments',
                'company_id' => null,
                'system_item' => 1,
            ],
            [
                'page_name' => 'Штат',
                'site_id' => '1',
                'page_title' => 'Штат компании',
                'page_description' => 'Штат компании',
                'page_alias' => '/staff',
                'company_id' => null,
                'system_item' => 1,
            ],
            [
                'page_name' => 'Сотрудники',
                'site_id' => '1',
                'page_title' => 'Сотрудники компани',
                'page_description' => 'Сотрудники компании',
                'page_alias' => '/employees',
                'company_id' => null,
                'system_item' => 1,
            ],
            [
                'page_name' => 'Тестовая страница для должностей',
                'site_id' => '1',
                'page_title' => 'Страница должности',
                'page_description' => 'Должность в компании',
                'page_alias' => '/home',
                'company_id' => null,
                'system_item' => null,
            ],
             [
                'page_name' => 'Сайты',
                'site_id' => '1',
                'page_title' => 'Сайты компании',
                'page_description' => 'Сайты компаний в системе, и сама система',
                'page_alias' => '/sites',
                'company_id' => null,
                'system_item' => 1,
            ],
            [
                'page_name' => 'Страницы сайта',
                'site_id' => '1',
                'page_title' => 'Страницы сайта',
                'page_description' => 'Страницы определенного сайта',
                'page_alias' => '/pages',
                'company_id' => null,
                'system_item' => 1,
            ],
            [
                'page_name' => 'Населенные пункты',
                'site_id' => '1',
                'page_title' => 'Населенные пункты',
                'page_description' => 'Области, районы и города',
                'page_alias' => '/cities',
                'company_id' => null,
                'system_item' => 1,
            ],
            [
                'page_name' => 'Должности',
                'site_id' => '1',
                'page_title' => 'Должности',
                'page_description' => 'Должности компании',
                'page_alias' => '/positions',
                'company_id' => null,
                'system_item' => 1,
            ],
            [
                'page_name' => 'Сущности',
                'site_id' => '1',
                'page_title' => 'Сущности',
                'page_description' => 'Сущности системы',
                'page_alias' => '/entities',
                'company_id' => null,
                'system_item' => 1,
            ],
            [
                'page_name' => 'Роли',
                'site_id' => '1',
                'page_title' => 'Группы доступа (роли)',
                'page_description' => 'Пользовательские группы доступа',
                'page_alias' => '/roles',
                'company_id' => null,
                'system_item' => 1,
            ],
            [
                'page_name' => 'Правила доступа',
                'site_id' => '1',
                'page_title' => 'Правила доступа зерегистрированные для системы',
                'page_description' => 'Правила доступа',
                'page_alias' => '/rights',
                'company_id' => null,
                'system_item' => 1,
            ],
            [
                'page_name' => 'Главная',
                'site_id' => '2',
                'page_title' => 'Воротная компания "Марс"',
                'page_description' => 'Откатные, секционые, распашные ворота.',
                'page_alias' => '/index',
                'company_id' => 1,
                'system_item' => null,
            ],

        ]);
    }
}
