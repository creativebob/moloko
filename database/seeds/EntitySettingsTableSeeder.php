<?php

use Illuminate\Database\Seeder;

class EntitySettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('entity_settings')->insert([
        	[
                'company_id' => null,
		        'name' => 'img_small_width',
		        'description' => 'Комментарий к настройке',
                'entity_id' => null,
                'entity' => null,

                'img_small_width' => 150,  // Ширина маленького изображения
                'img_small_height' => 99,  // Высота маленького изображения
                'img_medium_width' => 900,  // Ширина среднего изображения
                'img_medium_height' => 596,  // Высота среднего изображения
                'img_large_width' => 1200,  // Ширина большого изображения
                'img_large_height' => 795,  // Высота большого изображения
                'img_formats' => '.jpeg,.jpg,.png,.gif,.svg',  // Форматы изображения
                'img_min_width' => 1200,  // Минимальная ширина изображения
                'img_min_height' => 795,  // Минимальная высота изображения
                'img_max_size' => 8,  // Размер изображения   

                'author_id' => 1,
        	],

            [
                'company_id' => null,
                'name' => 'img_small_width',
                'description' => 'Нстройка аватра пользователя',
                'entity_id' => null,
                'entity' => 'users',

                'img_small_width' => 150,  // Ширина маленького изображения
                'img_small_height' => 99,  // Высота маленького изображения
                'img_medium_width' => 900,  // Ширина среднего изображения
                'img_medium_height' => 596,  // Высота среднего изображения
                'img_large_width' => 1200,  // Ширина большого изображения
                'img_large_height' => 795,  // Высота большого изображения
                'img_formats' => '.jpeg,.jpg,.png,.gif,.svg',  // Форматы изображения
                'img_min_width' => 1200,  // Минимальная ширина изображения
                'img_min_height' => 795,  // Минимальная высота изображения
                'img_max_size' => 8,  // Размер изображения   

                'author_id' => 1,
            ],

            [
                'company_id' => null,
                'name' => 'img_small_width',
                'description' => 'Настойка аватара товара',
                'entity_id' => null,
                'entity' => 'goods',

                'img_small_width' => 150,  // Ширина маленького изображения
                'img_small_height' => 99,  // Высота маленького изображения
                'img_medium_width' => 900,  // Ширина среднего изображения
                'img_medium_height' => 596,  // Высота среднего изображения
                'img_large_width' => 1200,  // Ширина большого изображения
                'img_large_height' => 795,  // Высота большого изображения
                'img_formats' => '.jpeg,.jpg,.png,.gif,.svg',  // Форматы изображения
                'img_min_width' => 1200,  // Минимальная ширина изображения
                'img_min_height' => 795,  // Минимальная высота изображения
                'img_max_size' => 8,  // Размер изображения   

                'author_id' => 1,
            ],

            [
                'company_id' => null,
                'name' => 'img_small_width',
                'description' => 'Настройка альбомной категории товаров',
                'entity_id' => 1,
                'entity' => 'albums_categories',

                'img_small_width' => 150,  // Ширина маленького изображения
                'img_small_height' => 99,  // Высота маленького изображения
                'img_medium_width' => 900,  // Ширина среднего изображения
                'img_medium_height' => 596,  // Высота среднего изображения
                'img_large_width' => 1200,  // Ширина большого изображения
                'img_large_height' => 795,  // Высота большого изображения
                'img_formats' => '.jpeg,.jpg,.png,.gif,.svg',  // Форматы изображения
                'img_min_width' => 1200,  // Минимальная ширина изображения
                'img_min_height' => 795,  // Минимальная высота изображения
                'img_max_size' => 8,  // Размер изображения   

                'author_id' => 1,
            ],
        ]);
    }
}
