<?php

use Illuminate\Database\Seeder;

class IndicatorsCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('indicators_categories')->insert([
    		[
    			'name' => 'Маркетинг',
    			'author_id' => 1,
    			'display' => 1,
    		],
            [
                'name' => 'Финансы',
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Производство',
                'author_id' => 1,
                'display' => 1,
            ],
    	]);
    }
}
