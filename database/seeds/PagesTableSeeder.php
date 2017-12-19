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
                'page_name' => 'Пользователи системы',
                'site_id' => '1',
                'page_title' => 'Пользователи системы',
                'page_description' => 'Пользователи системы',
                'page_alias' => '/users',
            ],
            [
                'page_name' => 'Компании',
                'site_id' => '1',
                'page_title' => 'Компании',
                'page_description' => 'Компании в системе автоматизации',
                'page_alias' => '/companies',
            ],
            [
                'page_name' => 'Сайты',
                'site_id' => '1',
                'page_title' => 'Сайты',
                'page_description' => 'Сайты компаний в системе, и сама система',
                'page_alias' => '/sites',
            ],
            [
                'page_name' => 'Страницы сайта',
                'site_id' => '1',
                'page_title' => 'Страницы сайта',
                'page_description' => 'Страницы определенного сайта',
                'page_alias' => '/pages',
            ],
        	[
		        'page_name' => 'Населенные пункты',
                'site_id' => '1',
		        'page_title' => 'Населенные пункты',
		        'page_description' => 'Области, районы и города',
                'page_alias' => '/cities',
        	],
            [
                'page_name' => 'Филиалы',
                'site_id' => '1',
                'page_title' => 'Филиалы',
                'page_description' => 'Филиалы и отделы',
                'page_alias' => '/departments',
            ],
             [
                'page_name' => 'Должности',
                'site_id' => '1',
                'page_title' => 'Должности',
                'page_description' => 'Должности компании',
                'page_alias' => '/positions',
            ],
            [
                'page_name' => 'Главная',
                'site_id' => '2',
                'page_title' => 'Воротная компания "Марс"',
                'page_description' => 'Откатные, секционые, распашные ворота.',
                'page_alias' => '/index',
            ],
        ]);
    }
}
