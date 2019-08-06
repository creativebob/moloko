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
                'alias' => 'lenght',
                'article' => 1,
                'process' => 0,
                'author_id' => 1,
                'sort' => 4,
            ],
            [
                'name' => 'Вес',
                'alias' => 'weight',
                'article' => 1,
                'process' => 0,
                'author_id' => 1,
                'sort' => 2,
            ],
            [
                'name' => 'Время',
                'alias' => 'time',
                'article' => 0,
                'process' => 1,
                'author_id' => 1,
                'sort' => 6,
            ],
            [
                'name' => 'Площадь',
                'alias' => 'area',
                'article' => 1,
                'process' => 0,
                'author_id' => 1,
                'sort' => 5,
            ],
            [
                'name' => 'Обьем',
                'alias' => 'volume',
                'article' => 1,
                'process' => 0,
                'author_id' => 1,
                'sort' => 3,
            ],
            [
                'name' => 'Количество',
                'alias' => 'count',
                'article' => 1,
                'process' => 1,
                'author_id' => 1,
                'sort' => 1,
            ],
            [
                'name' => 'Проценты',
                'alias' => 'percent',
                'article' => 0,
                'process' => 0,
                'author_id' => 1,
                'sort' => 7,
            ],
            [
                'name' => 'Валюта',
                'alias' => 'currency',
                'article' => 0,
                'process' => 0,
                'author_id' => 1,
                'sort' => 8,
            ],
        ]);

        $units_categories = UnitsCategory::get([
        	'id',
        	'name',
            'alias'
        ]);

        Unit::insert([
            // Длина
            [
                'name' => 'Миллиметр',
                'abbreviation' => 'мм',
                'ratio' => 0.001,
                'category_id' => $units_categories->where('alias', 'lenght')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Сантиметр',
                'abbreviation' => 'см',
                'ratio' => 0.01,
                'category_id' => $units_categories->where('alias', 'lenght')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Дециметр',
                'abbreviation' => 'дм',
                'ratio' => 0.1,
                'category_id' => $units_categories->where('alias', 'lenght')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Метр',
                'abbreviation' => 'м',
                'ratio' => 1,
                'category_id' => $units_categories->where('alias', 'lenght')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Километр',
                'abbreviation' => 'км',
                'ratio' => 1000,
                'category_id' => $units_categories->where('alias', 'lenght')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],

            // Масса
            [
                'name' => 'Миллиграмм',
                'abbreviation' => 'мг',
                'ratio' => 0.000001,
                'category_id' => $units_categories->where('alias', 'weight')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Грамм',
                'abbreviation' => 'г',
                'ratio' => 0.001,
                'category_id' => $units_categories->where('alias', 'weight')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Килограмм',
                'abbreviation' => 'кг',
                'ratio' => 1,
                'category_id' => $units_categories->where('alias', 'weight')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Центнер',
                'abbreviation' => 'ц',
                'ratio' => 100,
                'category_id' => $units_categories->where('alias', 'weight')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Тонна',
                'abbreviation' => 'т',
                'ratio' => 1000,
                'category_id' => $units_categories->where('alias', 'weight')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],

            // Время
            [
                'name' => 'Милисекунда',
                'abbreviation' => 'мс',
                'ratio' => 0.001,
                'category_id' => $units_categories->where('alias', 'time')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Секунда',
                'abbreviation' => 'с',
                'ratio' => 1,
                'category_id' => $units_categories->where('alias', 'time')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Минута',
                'abbreviation' => 'мин',
                'ratio' => 60,
                'category_id' => $units_categories->where('alias', 'time')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Час',
                'abbreviation' => 'ч',
                'ratio' => 3600,
                'category_id' => $units_categories->where('alias', 'time')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Сутки',
                'abbreviation' => 'сут',
                'ratio' => 86400,
                'category_id' => $units_categories->where('alias', 'time')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Неделя',
                'abbreviation' => 'нед',
                'ratio' => 604800,
                'category_id' => $units_categories->where('alias', 'time')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Месяц',
                'abbreviation' => 'мес',
                'ratio' => 2592000,
                'category_id' => $units_categories->where('alias', 'time')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Квартал',
                'abbreviation' => 'кварт',
                'ratio' => 7776000,
                'category_id' => $units_categories->where('alias', 'time')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Полгода',
                'abbreviation' => 'полгода',
                'ratio' => 15552000,
                'category_id' => $units_categories->where('alias', 'time')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Год',
                'abbreviation' => 'год',
                'ratio' => 31536000,
                'category_id' => $units_categories->where('alias', 'time')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],

            // Площадь
            [
                'name' => 'Квадратный миллиметр',
                'abbreviation' => 'кв. мм',
                'ratio' => 0.001,
                'category_id' => $units_categories->where('alias', 'area')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Квадратный сантиметр',
                'abbreviation' => 'кв. см',
                'ratio' => 0.01,
                'category_id' => $units_categories->where('alias', 'area')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Квадратный дециметр',
                'abbreviation' => 'кв. дм',
                'ratio' => 0.1,
                'category_id' => $units_categories->where('alias', 'area')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Квадратный метр',
                'abbreviation' => 'кв. м',
                'ratio' => 1,
                'category_id' => $units_categories->where('alias', 'area')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Квадратный километр',
                'abbreviation' => 'кв. км',
                'ratio' => 1000,
                'category_id' => $units_categories->where('alias', 'area')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Гектар',
                'abbreviation' => 'га',
                'ratio' => 10000,
                'category_id' => $units_categories->where('alias', 'area')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],

            // Обьем
            [
                'name' => 'Кубический сантиметр',
                'abbreviation' => 'куб. см',
                'ratio' => 0.000001,
                'category_id' => $units_categories->where('alias', 'volume')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Миллилитр',
                'abbreviation' => 'мл',
                'ratio' => 0.000001,
                'category_id' => $units_categories->where('alias', 'volume')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Кубический дециметр',
                'abbreviation' => 'куб. дм',
                'ratio' => 0.001,
                'category_id' => $units_categories->where('alias', 'volume')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Литр',
                'abbreviation' => 'л',
                'ratio' => 0.001,
                'category_id' => $units_categories->where('alias', 'volume')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],
            [
                'name' => 'Кубический метр',
                'abbreviation' => 'куб. м',
                'ratio' => 1,
                'category_id' => $units_categories->where('alias', 'volume')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],

            // Количество
            [
                'name' => 'Штука',
                'abbreviation' => 'шт',
                'ratio' => 1,
                'category_id' => $units_categories->where('alias', 'count')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],

            // Проценты
            [
                'name' => 'Процент',
                'abbreviation' => '%',
                'ratio' => 1,
                'category_id' => $units_categories->where('alias', 'percent')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
            ],

            // Валюта
            [
                'name' => 'Рубль',
                'abbreviation' => '₽',
                'ratio' => 1,
                'category_id' => $units_categories->where('alias', 'currency')->first()->id,
                'system' => true,
                'author_id' => 1,
                'moderation' => false,
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
            //  'system' => false,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],
            // [
            //  'name' => 'Количество вещества',
            //  'unit_id' => 'моль',
            //  'abbreviation' => 'моль',
            //  'system' => false,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],
            // [
            //  'name' => 'Частота',
            //  'unit_id' => 'герц',
            //  'abbreviation' => 'Гц',
            //  'system' => false,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],
            // [
            //  'name' => 'Сила',
            //  'unit_id' => 'ньютон',
            //  'abbreviation' => 'Н',
            //  'system' => false,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],
            // [
            //  'name' => 'Энергия',
            //  'unit_id' => 'джоуль',
            //  'abbreviation' => 'Дж',
            //  'system' => false,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],
            // [
            //  'name' => 'Мощность',
            //  'unit_id' => 'ватт',
            //  'abbreviation' => 'Вт',
            //  'system' => false,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],
            // [
            //  'name' => 'Давление',
            //  'unit_id' => 'паскаль',
            //  'abbreviation' => 'Па',
            //  'system' => false,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],
            // [
            //  'name' => 'Сопротивление',
            //  'unit_id' => 'ом',
            //  'abbreviation' => 'Ом',
            //  'system' => false,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],

// [
            //  'name' => 'Кубический миллиметр',
            //  'abbreviation' => 'куб. см',
            //  'ratio' => 0.001,
            //  'units_category_id' => 5,
            //  'system' => true,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],

// [
            //  'name' => 'Пара',
            //  'abbreviation' => 'пара',
            //  'ratio' => 2,
            //  'units_category_id' => 6,
            //  'system' => true,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],
            // [
            //  'name' => 'Упаковка',
            //  'abbreviation' => 'упак',
            //  'ratio' => 1,
            //  'units_category_id' => 6,
            //  'system' => true,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],
            // [
            //  'name' => 'Рулон',
            //  'abbreviation' => 'рул',
            //  'ratio' => 1,
            //  'units_category_id' => 6,
            //  'system' => true,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],
            // [
            //  'name' => 'Коробка',
            //  'abbreviation' => 'кор',
            //  'ratio' => 1,
            //  'units_category_id' => 6,
            //  'system' => true,
            //  'author_id' => 1,
            //  'moderation' => false,
            // ],
}
