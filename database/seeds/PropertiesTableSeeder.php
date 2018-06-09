<?php

use Illuminate\Database\Seeder;

class PropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('properties')->insert([
    		[
    			'name' => 'Ширина',
    			'units_category_id' => 1,
    			'type' => 'numeric',
    		],
    		[
    			'name' => 'Длина',
    			'units_category_id' => 1,
    			'type' => 'numeric',
    		],
    		[
    			'name' => 'Высота',
    			'units_category_id' => 1,
    			'type' => 'numeric',
    		],
    		[
    			'name' => 'Глубина',
    			'units_category_id' => 1,
    			'type' => 'numeric',
    		],
    		[
    			'name' => 'Расстояние',
    			'units_category_id' => 1,
    			'type' => 'numeric',
    		],
    		[
    			'name' => 'Количество',
    			'units_category_id' => 6,
    			'type' => 'numeric',
    		],
    		[
    			'name' => 'Вес',
    			'units_category_id' => 2,
    			'type' => 'numeric',
    		],
    		[
    			'name' => 'Площадь',
    			'units_category_id' => 4,
    			'type' => 'numeric',
    		],
    		[
    			'name' => 'Обьем',
    			'units_category_id' => 5,
    			'type' => 'numeric',
    		],

    	]);
    }
}
