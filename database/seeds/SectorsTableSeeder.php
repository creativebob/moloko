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
		        'sector_name' => 'Строительство',
		        'sector_parent_id' => null,
                'industry_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'sector_name' => 'Производство',
		        'sector_parent_id' => null,
                'industry_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'sector_name' => 'Транспорт',
		        'sector_parent_id' => null,
                'industry_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'sector_name' => 'Интернет',
		        'sector_parent_id' => null,
                'industry_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'sector_name' => 'Торговля',
		        'sector_parent_id' => null,
                'industry_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'sector_name' => 'Туризм',
		        'sector_parent_id' => null,
                'industry_status' => 1,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'sector_name' => 'Заборы',
		        'sector_parent_id' => 1,
                'industry_status' => null,
                'author_id' => 1,
                // 'industry_id' => 1,
        	],
        	[
		        'sector_name' => 'Ворота',
		        'sector_parent_id' => 2,
                'industry_status' => null,
                'author_id' => 1,
                // 'industry_id' => 2,
        	],
        	[
		        'sector_name' => 'Окна',
		        'sector_parent_id' => 2,
                'industry_status' => null,
                'author_id' => 1,
                // 'industry_id' => 2,
        	],
        	[
		        'sector_name' => 'Грузоперевозки',
		        'sector_parent_id' => 3,
                'industry_status' => null,
                'author_id' => 1,
                // 'industry_id' => 3,
        	],
        	[
		        'sector_name' => 'Логистика',
		        'sector_parent_id' => 3,
                'industry_status' => null,
                'author_id' => 1,
                // 'industry_id' => 3,
        	],
        	[
		        'sector_name' => 'Такси',
		        'sector_parent_id' => 3,
                'industry_status' => null,
                'author_id' => 1,
                // 'industry_id' => 3,
        	],
        	


 
        ]);
    }
}
