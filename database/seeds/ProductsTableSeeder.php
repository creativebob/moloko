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
    		],
            [
                'name' => 'Секционные ворота',
                'products_category_id' => 3,
                'company_id' => 1, 
                'author_id' => 4, 
                'manufacturer_id' => null,
            ],
            [
                'name' => 'Забор',
                'products_category_id' => 4,
                'company_id' => 1, 
                'author_id' => 4, 
                'manufacturer_id' => null,
            ],
    	]);
    }
}
