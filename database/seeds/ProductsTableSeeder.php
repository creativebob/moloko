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
    			'products_category_id' => 1,
    			'company_id' => 1, 
    			'author_id' => 4, 
                'unit_id' => 26,

                'display' => 1,
    		],
            [
                'name' => 'Секционные ворота',
                'products_category_id' => 1,
                'company_id' => 1, 
                'author_id' => 4, 
                'unit_id' => 26, 

                'display' => 1,
            ],

    	]);
    }
}
