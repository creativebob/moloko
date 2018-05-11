<?php

use Illuminate\Database\Seeder;

class SectorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sectors')->insert([
        	[
		        'name' => 'Строительство',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'Производство',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'Транспорт',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'Интернет',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'Торговля',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'Туризм',
		        'parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'Заборы',
		        'parent_id' => 1,
                'category_status' => null,
                'author_id' => 1,
                // 'industry_id' => 1,
        	],
        	[
		        'name' => 'Ворота',
		        'parent_id' => 2,
                'category_status' => null,
                'author_id' => 1,
                // 'industry_id' => 2,
        	],
        	[
		        'name' => 'Окна',
		        'parent_id' => 2,
                'category_status' => null,
                'author_id' => 1,
                // 'industry_id' => 2,
        	],
        	[
		        'name' => 'Грузоперевозки',
		        'parent_id' => 3,
                'category_status' => null,
                'author_id' => 1,
                // 'industry_id' => 3,
        	],
        	[
		        'name' => 'Логистика',
		        'parent_id' => 3,
                'category_status' => null,
                'author_id' => 1,
                // 'industry_id' => 3,
        	],
        	[
		        'name' => 'Такси',
		        'parent_id' => 3,
                'category_status' => null,
                'author_id' => 1,
                // 'industry_id' => 3,
        	],
        ]);
    }
}
