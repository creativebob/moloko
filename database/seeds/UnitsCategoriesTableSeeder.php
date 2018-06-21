<?php

use Illuminate\Database\Seeder;

class UnitsCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('units_categories')->insert([
    		[
    			'name' => 'Длина', 
    			'unit' => 'метр', 
    			'abbreviation' => 'м', 
    			'system_item' => null, 
    			'author_id' => 1, 
    			'moderation' => null, 
    		],
    		[
    			'name' => 'Масса', 
    			'unit' => 'килограмм', 
    			'abbreviation' => 'кг', 
    			'system_item' => null, 
    			'author_id' => 1, 
    			'moderation' => null, 
    		],
    		[
    			'name' => 'Время', 
    			'unit' => 'секунда', 
    			'abbreviation' => 'с', 
    			'system_item' => null, 
    			'author_id' => 1, 
    			'moderation' => null, 
    		],
    		[
    			'name' => 'Площадь', 
    			'unit' => 'квадратный метр', 
    			'abbreviation' => 'кв. м', 
    			'system_item' => null, 
    			'author_id' => 1, 
    			'moderation' => null, 
    		],
    		[
    			'name' => 'Обьем', 
    			'unit' => 'кубометр', 
    			'abbreviation' => 'куб. м', 
    			'system_item' => null, 
    			'author_id' => 1, 
    			'moderation' => null, 
    		],
    		[
    			'name' => 'Количество', 
    			'unit' => 'штука', 
    			'abbreviation' => 'шт', 
    			'system_item' => null, 
    			'author_id' => 1, 
    			'moderation' => null, 
    		],
            [
                'name' => 'Проценты', 
                'unit' => 'процент', 
                'abbreviation' => '%', 
                'system_item' => null, 
                'author_id' => 1, 
                'moderation' => null, 
            ],

    		// [
    		// 	'name' => 'Сила электрического тока', 
    		// 	'unit' => 'ампер', 
    		// 	'abbreviation' => 'А', 
    		// 	'system_item' => null, 
    		// 	'author_id' => 1, 
    		// 	'moderation' => null, 
    		// ],
    		// [
    		// 	'name' => 'Количество вещества', 
    		// 	'unit' => 'моль', 
    		// 	'abbreviation' => 'моль', 
    		// 	'system_item' => null, 
    		// 	'author_id' => 1, 
    		// 	'moderation' => null, 
    		// ],
    		// [
    		// 	'name' => 'Частота', 
    		// 	'unit' => 'герц', 
    		// 	'abbreviation' => 'Гц', 
    		// 	'system_item' => null, 
    		// 	'author_id' => 1, 
    		// 	'moderation' => null, 
    		// ],
    		// [
    		// 	'name' => 'Сила', 
    		// 	'unit' => 'ньютон', 
    		// 	'abbreviation' => 'Н', 
    		// 	'system_item' => null, 
    		// 	'author_id' => 1, 
    		// 	'moderation' => null, 
    		// ],
    		// [
    		// 	'name' => 'Энергия', 
    		// 	'unit' => 'джоуль', 
    		// 	'abbreviation' => 'Дж', 
    		// 	'system_item' => null, 
    		// 	'author_id' => 1, 
    		// 	'moderation' => null, 
    		// ],
    		// [
    		// 	'name' => 'Мощность', 
    		// 	'unit' => 'ватт', 
    		// 	'abbreviation' => 'Вт', 
    		// 	'system_item' => null, 
    		// 	'author_id' => 1, 
    		// 	'moderation' => null, 
    		// ],
    		// [
    		// 	'name' => 'Давление', 
    		// 	'unit' => 'паскаль', 
    		// 	'abbreviation' => 'Па', 
    		// 	'system_item' => null, 
    		// 	'author_id' => 1, 
    		// 	'moderation' => null, 
    		// ],
    		// [
    		// 	'name' => 'Сопротивление', 
    		// 	'unit' => 'ом', 
    		// 	'abbreviation' => 'Ом', 
    		// 	'system_item' => null, 
    		// 	'author_id' => 1, 
    		// 	'moderation' => null, 
    		// ],
    	]);
    }
}
