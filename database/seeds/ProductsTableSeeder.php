<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('products')->insert([
    		[
    			'name' => 'Откатные ворота',
    			'products_category_id' => 2,
    			'company_id' => 1, 
    			'author_id' => 4, 
                'manufacturer_id' => 1,
                'unit_id' => 26, 
    		],
            [
                'name' => 'Секционные ворота',
                'products_category_id' => 3,
                'company_id' => 1, 
                'author_id' => 4, 
                'manufacturer_id' => null,
                'unit_id' => 26, 
            ],
            [
                'name' => 'Забор',
                'products_category_id' => 4,
                'company_id' => 1, 
                'author_id' => 4, 
                'manufacturer_id' => null,
                'unit_id' => 26, 
            ],

            [
                'name' => 'Труба',
                'products_category_id' => 26,
                'company_id' => 1, 
                'author_id' => 4, 
                'manufacturer_id' => null,
                'unit_id' => 26, 
            ],

             [
                'name' => 'Прутик',
                'products_category_id' => 26,
                'company_id' => 1, 
                'author_id' => 4, 
                'manufacturer_id' => null,
                'unit_id' => 26, 
            ],

            [
                'name' => 'Полуфабрик',
                'products_category_id' => 27,
                'company_id' => 1, 
                'author_id' => 4, 
                'manufacturer_id' => null,
                'unit_id' => 26, 
            ],

            // Шторка
            [
                'name' => 'Блекаут вензель розовый',
                'products_category_id' => 11,
                'company_id' => 4, 
                'author_id' => 15, 
                'manufacturer_id' => 5,
                'unit_id' => 4, 
            ],
            [
                'name' => 'Блекаут вензель терракотовый',
                'products_category_id' => 11,
                'company_id' => 4, 
                'author_id' => 15, 
                'manufacturer_id' => 5,
                'unit_id' => 4, 
            ],
            [
                'name' => 'Блекаут БАБОЧКИ',
                'products_category_id' => 11,
                'company_id' => 4, 
                'author_id' => 15, 
                'manufacturer_id' => 5,
                'unit_id' => 4, 
            ],
            [
                'name' => 'Блекаут ЛИСТЬЯ',
                'products_category_id' => 11,
                'company_id' => 4, 
                'author_id' => 15, 
                'manufacturer_id' => 5,
                'unit_id' => 4, 
            ],
            [
                'name' => '',
                'products_category_id' => 11,
                'company_id' => 4, 
                'author_id' => 15, 
                'manufacturer_id' => 5,
                'unit_id' => 4, 
            ],
            [
                'name' => '',
                'products_category_id' => 11,
                'company_id' => 4, 
                'author_id' => 15, 
                'manufacturer_id' => 5,
                'unit_id' => 4, 
            ],
            [
                'name' => '',
                'products_category_id' => 11,
                'company_id' => 4, 
                'author_id' => 15, 
                'manufacturer_id' => 5,
                'unit_id' => 4, 
            ],
            [
                'name' => '',
                'products_category_id' => 11,
                'company_id' => 4, 
                'author_id' => 15, 
                'manufacturer_id' => 5,
                'unit_id' => 4, 
            ],
    	]);
    }
}
