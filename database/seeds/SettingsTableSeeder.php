<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
        	[
		        'name' => 'img_small_width',
		        'description' => 'Ширина маленького изображения',
                'value' => 150,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'img_small_height',
		        'description' => 'Высота маленького изображения',
                'value' => 99,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'img_medium_width',
		        'description' => 'Ширина среднего изображения',
                'value' => 900,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'img_medium_height',
		        'description' => 'Высота среднего изображения',
                'value' => 596,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'img_large_width',
		        'description' => 'Ширина большого изображения',
                'value' => 1200,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'img_large_height',
		        'description' => 'Высота большого изображения',
                'value' => 795,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'img_formats',
		        'description' => 'Форматы изображения',
                'value' => '.jpeg,.jpg,.png,.gif,.svg',
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'img_min_width',
		        'description' => 'Минимальная ширина изображения',
                'value' => 1200,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'img_min_height',
		        'description' => 'Минимальная высота изображения',
                'value' => 795,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        	[
		        'name' => 'img_max_size',
		        'description' => 'Размер изображения',
                'value' => 8,
                'author_id' => 1,
                // 'industry_id' => null,
        	],
        ]);
    }
}
