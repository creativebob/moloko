<?php

use Illuminate\Database\Seeder;

use App\UnitsCategory;
use App\Unit;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	UnitsCategory::insert([
    		[
    			'name' => 'Длина',
                'article' => 1,
                'process' => 0,
                'author_id' => 1,
            ],
            [
                'name' => 'Масса',
                'article' => 1,
                'process' => 0,
                'author_id' => 1,
            ],
            [
                'name' => 'Время',
                'article' => 0,
                'process' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Площадь',
                'article' => 1,
                'process' => 0,
                'author_id' => 1,
            ],
            [
                'name' => 'Обьем',
                'article' => 1,
                'process' => 0,
                'author_id' => 1,
            ],
            [
                'name' => 'Количество',
                'article' => 1,
                'process' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Проценты',
                'article' => 1,
                'process' => 0,
                'author_id' => 1,
            ],
            [
                'name' => 'Валюта',
                'article' => 1,
                'process' => 0,
                'author_id' => 1,
            ],
        ]);

        $units_categories = UnitsCategory::get([
        	'id',
        	'name'
        ]);

        Unit::insert([
            // Длина
            [
                'name' => 'Миллиметр',
                'abbreviation' => 'мм',
                'ratio' => 0.001,
                'category_id' => $units_categories->where('name', 'Длина')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Сантиметр',
                'abbreviation' => 'см',
                'ratio' => 0.01,
                'category_id' => $units_categories->where('name', 'Длина')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Дециметр',
                'abbreviation' => 'дм',
                'ratio' => 0.1,
                'category_id' => $units_categories->where('name', 'Длина')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Метр',
                'abbreviation' => 'м',
                'ratio' => 1,
                'category_id' => $units_categories->where('name', 'Длина')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Километр',
                'abbreviation' => 'км',
                'ratio' => 1000,
                'category_id' => $units_categories->where('name', 'Длина')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],

            // Масса
            [
                'name' => 'Миллиграмм',
                'abbreviation' => 'мг',
                'ratio' => 0.000001,
                'category_id' => $units_categories->where('name', 'Масса')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Грамм',
                'abbreviation' => 'г',
                'ratio' => 0.001,
                'category_id' => $units_categories->where('name', 'Масса')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Килограмм',
                'abbreviation' => 'кг',
                'ratio' => 1,
                'category_id' => $units_categories->where('name', 'Масса')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Центнер',
                'abbreviation' => 'ц',
                'ratio' => 100,
                'category_id' => $units_categories->where('name', 'Масса')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Тонна',
                'abbreviation' => 'т',
                'ratio' => 1000,
                'category_id' => $units_categories->where('name', 'Масса')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],

            // Время
            [
                'name' => 'Милисекунда',
                'abbreviation' => 'мс',
                'ratio' => 0.001,
                'category_id' => $units_categories->where('name', 'Время')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Секунда',
                'abbreviation' => 'с',
                'ratio' => 1,
                'category_id' => $units_categories->where('name', 'Время')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Минута',
                'abbreviation' => 'мин',
                'ratio' => 60,
                'category_id' => $units_categories->where('name', 'Время')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Час',
                'abbreviation' => 'ч',
                'ratio' => 3600,
                'category_id' => $units_categories->where('name', 'Время')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],

            // Площадь
            [
                'name' => 'Квадратный миллиметр',
                'abbreviation' => 'кв. мм',
                'ratio' => 0.001,
                'category_id' => $units_categories->where('name', 'Площадь')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Квадратный сантиметр',
                'abbreviation' => 'кв. см',
                'ratio' => 0.01,
                'category_id' => $units_categories->where('name', 'Площадь')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Квадратный дециметр',
                'abbreviation' => 'кв. дм',
                'ratio' => 0.1,
                'category_id' => $units_categories->where('name', 'Площадь')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Квадратный метр',
                'abbreviation' => 'кв. м',
                'ratio' => 1,
                'category_id' => $units_categories->where('name', 'Площадь')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Квадратный километр',
                'abbreviation' => 'кв. км',
                'ratio' => 1000,
                'category_id' => $units_categories->where('name', 'Площадь')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Гектар',
                'abbreviation' => 'га',
                'ratio' => 10000,
                'category_id' => $units_categories->where('name', 'Площадь')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],

            // Обьем
            [
                'name' => 'Кубический сантиметр',
                'abbreviation' => 'куб. см',
                'ratio' => 0.000001,
                'category_id' => $units_categories->where('name', 'Обьем')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Миллилитр',
                'abbreviation' => 'мл',
                'ratio' => 0.000001,
                'category_id' => $units_categories->where('name', 'Обьем')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Кубический дециметр',
                'abbreviation' => 'куб. дм',
                'ratio' => 0.001,
                'category_id' => $units_categories->where('name', 'Обьем')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Литр',
                'abbreviation' => 'л',
                'ratio' => 0.001,
                'category_id' => $units_categories->where('name', 'Обьем')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Кубический метр',
                'abbreviation' => 'куб. м',
                'ratio' => 1,
                'category_id' => $units_categories->where('name', 'Обьем')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],

            // Количество
            [
                'name' => 'Штука',
                'abbreviation' => 'шт',
                'ratio' => 1,
                'category_id' => $units_categories->where('name', 'Количество')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],

            // Проценты
            [
                'name' => 'Процент',
                'abbreviation' => '%',
                'ratio' => 1,
                'category_id' => $units_categories->where('name', 'Проценты')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],

            // Валюта
            [
                'name' => 'Рубль',
                'abbreviation' => '₽',
                'ratio' => 1,
                'category_id' => $units_categories->where('name', 'Валюта')->first()->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
        ]);

$units = Unit::get([
    'id',
    'name'
]);

$array = [
    'Длина' => 'Метр',
    'Масса' => 'Килограмм',
    'Время' => 'Секунда',
    'Площадь' => 'Квадратный метр',
    'Обьем' => 'Кубический метр',
    'Количество' => 'Штука',
    'Проценты' => 'Процент',
    'Валюта' => 'Рубль',
];

foreach ($array as $units_category_name => $unit_name) {
    UnitsCategory::where('name', $units_category_name)->update([
        'unit_id' => $units->where('name', $unit_name)->first()->id,
    ]);
}

}

// [
            //  'name' => 'Сила электрического тока',
            //  'unit_id' => 'ампер',
            //  'abbreviation' => 'А',
            //  'system_item' => null,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],
            // [
            //  'name' => 'Количество вещества',
            //  'unit_id' => 'моль',
            //  'abbreviation' => 'моль',
            //  'system_item' => null,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],
            // [
            //  'name' => 'Частота',
            //  'unit_id' => 'герц',
            //  'abbreviation' => 'Гц',
            //  'system_item' => null,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],
            // [
            //  'name' => 'Сила',
            //  'unit_id' => 'ньютон',
            //  'abbreviation' => 'Н',
            //  'system_item' => null,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],
            // [
            //  'name' => 'Энергия',
            //  'unit_id' => 'джоуль',
            //  'abbreviation' => 'Дж',
            //  'system_item' => null,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],
            // [
            //  'name' => 'Мощность',
            //  'unit_id' => 'ватт',
            //  'abbreviation' => 'Вт',
            //  'system_item' => null,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],
            // [
            //  'name' => 'Давление',
            //  'unit_id' => 'паскаль',
            //  'abbreviation' => 'Па',
            //  'system_item' => null,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],
            // [
            //  'name' => 'Сопротивление',
            //  'unit_id' => 'ом',
            //  'abbreviation' => 'Ом',
            //  'system_item' => null,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],

// [
            //  'name' => 'Кубический миллиметр',
            //  'abbreviation' => 'куб. см',
            //  'ratio' => 0.001,
            //  'units_category_id' => 5,
            //  'system_item' => 1,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],

// [
            //  'name' => 'Пара',
            //  'abbreviation' => 'пара',
            //  'ratio' => 2,
            //  'units_category_id' => 6,
            //  'system_item' => 1,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],
            // [
            //  'name' => 'Упаковка',
            //  'abbreviation' => 'упак',
            //  'ratio' => 1,
            //  'units_category_id' => 6,
            //  'system_item' => 1,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],
            // [
            //  'name' => 'Рулон',
            //  'abbreviation' => 'рул',
            //  'ratio' => 1,
            //  'units_category_id' => 6,
            //  'system_item' => 1,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],
            // [
            //  'name' => 'Коробка',
            //  'abbreviation' => 'кор',
            //  'ratio' => 1,
            //  'units_category_id' => 6,
            //  'system_item' => 1,
            //  'author_id' => 1,
            //  'moderation' => null,
            // ],
}
