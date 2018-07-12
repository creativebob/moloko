<?php

use Illuminate\Database\Seeder;

class SectorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sectors')->insert([
        	[
		        'name' => 'Строительство',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'Производство',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'Транспорт',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],


            [
                'name' => 'Дом, ремонт',
                'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
            ],
                [
                    'name' => 'Двери и окна',
                    'parent_id' => 4,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],
                [
                    'name' => 'Дизайн студии',
                    'parent_id' => 4,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],
                [
                    'name' => 'Мебель и предметы интерьера',
                    'parent_id' => 4,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],
                [
                    'name' => 'Текстиль',
                    'parent_id' => 4,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],



            [
                'name' => 'Продукты питания',
                'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
            ],
                [
                    'name' => 'Продукты',
                    'parent_id' => 9,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],
                [
                    'name' => 'Доставка еды',
                    'parent_id' => 9,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],




        	[
		        'name' => 'Интернет',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'Торговля',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'Туризм',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],

                [
                    'name' => 'Отели, хостелы',
                    'parent_id' => 14,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],
                [
                    'name' => 'Туры и турагентства',
                    'parent_id' => 14,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],





            [
                'name' => 'Медицина',
                'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
            ],
                [
                    'name' => 'Аптеки',
                    'parent_id' => 17,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],
                [
                    'name' => 'Косметология и пластическая хирургия',
                    'parent_id' => 17,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],
                [
                    'name' => 'Оптика',
                    'parent_id' => 17,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],
                [
                    'name' => 'Психология',
                    'parent_id' => 17,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],
                [
                    'name' => 'Стоматология',
                    'parent_id' => 17,
                    'category_status' => 1,
                    'author_id' => 1,
                    // 'industry_id' => null,
                ],


        	[
		        'name' => 'Заборы',
		        'parent_id' => 1,
                'category_status' => null,
                'author_id' => 1,
                // 'industry_id' => 1,
        	],
        	[
		        'name' => 'Ворота',
		        'parent_id' => 1,
                'category_status' => null,
                'author_id' => 1,
                // 'industry_id' => 2,
        	],
        	[
		        'name' => 'Грузоперевозки',
		        'parent_id' => 3,
                'category_status' => null,
                'author_id' => 1,
                // 'industry_id' => 3,
        	],
        	[
		        'name' => 'Логистика',
		        'parent_id' => 3,
                'category_status' => null,
                'author_id' => 1,
                // 'industry_id' => 3,
        	],
        	[
		        'name' => 'Такси',
		        'parent_id' => 3,
                'category_status' => null,
                'author_id' => 1,
                // 'industry_id' => 3,
        	],
        ]);
    }
}
