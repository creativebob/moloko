<?php

use Illuminate\Database\Seeder;

class ProductsGroupesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products_groups')->insert([
    		[
    			'name' => 'Откатные ворота',
    			'products_category_id' => 2,
    			'company_id' => 1, 
    			'author_id' => 4, 
                'unit_id' => 26,
    		],
            [
                'name' => 'Секционные ворота',
                'products_category_id' => 3,
                'company_id' => 1, 
                'author_id' => 4, 
                'unit_id' => 26,
            ],
            [
                'name' => 'Забор',
                'products_category_id' => 4,
                'company_id' => 1, 
                'author_id' => 4, 
                'unit_id' => 26,
            ],
            [
                'name' => 'Профлист',
                'products_category_id' => 27,
                'company_id' => 1, 
                'author_id' => 4, 
                'unit_id' => 26,
            ],
    	]);
    }
}
