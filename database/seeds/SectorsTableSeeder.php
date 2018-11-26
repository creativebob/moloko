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
                'tag' => null,
        	],
        	[
		        'name' => 'Производство',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                'tag' => null,
        	],
        	[
		        'name' => 'Транспорт',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                'tag' => null,
        	],


            [
                'name' => 'Дом, ремонт',
                'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                'tag' => null,
            ],
                [
                    'name' => 'Двери и окна',
                    'parent_id' => 4,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Дизайн студии',
                    'parent_id' => 4,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Мебель и предметы интерьера',
                    'parent_id' => 4,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Текстиль',
                    'parent_id' => 4,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],



            [
                'name' => 'Продукты питания',
                'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                'tag' => null,
            ],
                [
                    'name' => 'Продукты',
                    'parent_id' => 9,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Доставка еды',
                    'parent_id' => 9,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],




        	[
		        'name' => 'Интернет',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                'tag' => null,
        	],
        	[
		        'name' => 'Торговля',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                'tag' => null,
        	],
        	[
		        'name' => 'Туризм',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                'tag' => null,
        	],

                [
                    'name' => 'Отели, хостелы',
                    'parent_id' => 14,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Туры и турагентства',
                    'parent_id' => 14,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],





            [
                'name' => 'Медицина',
                'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                'tag' => null,
            ],
                [
                    'name' => 'Аптеки',
                    'parent_id' => 17,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Косметология и пластическая хирургия',
                    'parent_id' => 17,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Оптика',
                    'parent_id' => 17,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Психология',
                    'parent_id' => 17,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Стоматология',
                    'parent_id' => 17,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],


        	[
		        'name' => 'Заборы',
		        'parent_id' => 1,
                'category_status' => null,
                'author_id' => 1,
                'tag' => null,
        	],
        	[
		        'name' => 'Ворота',
		        'parent_id' => 1,
                'category_status' => null,
                'author_id' => 1,
                'tag' => null,
        	],
        	[
		        'name' => 'Грузоперевозки',
		        'parent_id' => 3,
                'category_status' => null,
                'author_id' => 1,
                'tag' => null,
        	],
        	[
		        'name' => 'Логистика',
		        'parent_id' => 3,
                'category_status' => null,
                'author_id' => 1,
                'tag' => null,
        	],
        	[
		        'name' => 'Такси',
		        'parent_id' => 3,
                'category_status' => null,
                'author_id' => 1,
                'tag' => null,
        	],


            [
                'name' => 'Недвижимость', // 28
                'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                'tag' => null,
            ],
                [
                    'name' => 'Агенство недвижимости',
                    'parent_id' => 28,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Аренда недвижимости',
                    'parent_id' => 28,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Жилая недвижимость',
                    'parent_id' => 28,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Загородная недвижимость',
                    'parent_id' => 28,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],


            [
                'name' => 'Одежда и обувь', // 33
                'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                'tag' => null,
            ],
                [
                    'name' => 'Аксессуары',
                    'parent_id' => 33,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Мужская и женская одежда',
                    'parent_id' => 33,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],


            [
                'name' => 'Подарки и сувениры', // 36
                'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                'tag' => null,
            ],
                [
                    'name' => 'Магазин подарков',
                    'parent_id' => 36,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],
                [
                    'name' => 'Услуги по организации праздников',
                    'parent_id' => 36,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => null,
                ],

            [
                'name' => 'Финансовые услуги', // 39
                'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                'tag' => null,
            ],
                [
                    'name' => 'Банк',
                    'parent_id' => 39,
                    'category_status' => null,
                    'author_id' => 1,
                    'tag' => 'bank',
                ],


        ]);
    }
}
