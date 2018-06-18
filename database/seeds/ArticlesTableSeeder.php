<?php

use Illuminate\Database\Seeder;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('articles')->insert([
    		[
    			'name' => 'Откатные ворота',
    			'product_id' => 1,
    			'cost' => 30000,
    			'price' => 35000,
    			'company_id' => 1, 
    			'author_id' => 4, 
    		],
    		[
    			'name' => 'Секционные ворота',
    			'product_id' => 2,
    			'cost' => 24000,
    			'price' => null,
    			'company_id' => 1, 
    			'author_id' => 4, 
    		],
    		[
    			'name' => 'Забор',
    			'product_id' => 3,
    			'cost' => null,
    			'price' => null,
    			'company_id' => 1, 
    			'author_id' => 4, 
    		],
    		[
    			'name' => 'Труба',
    			'product_id' => 4,
    			'cost' => 1000,
    			'price' => null,
    			'company_id' => 1, 
    			'author_id' => 4, 
    		],
    		[
    			'name' => 'Прутик',
    			'product_id' => 5,
    			'cost' => 100,
    			'price' => null,
    			'company_id' => 1, 
    			'author_id' => 4, 
    		],
    		[
    			'name' => 'Полуфабрик',
    			'product_id' => 6,
    			'cost' => 5000,
    			'price' => 7000,
    			'company_id' => 1, 
    			'author_id' => 4, 
    		],

    	]);
    }
}
