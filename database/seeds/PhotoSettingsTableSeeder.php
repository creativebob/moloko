<?php

use Illuminate\Database\Seeder;

use App\PhotoSetting;

class PhotoSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PhotoSetting::insert([
        	[
                'company_id' => null,
		        'name' => 'Общие настройки',
		        'description' => 'Комментарий к настройке',

                'quality' => '80',
                'store_format' => 'jpg',

                'photo_settings_id' => null,
                'photo_settings_type' => null,

                'crop_mode' => 2,

                'img_small_width' => 150,  // Ширина маленького изображения
                'img_small_height' => 99,  // Высота маленького изображения
                'img_medium_width' => 440,  // Ширина среднего изображения
                'img_medium_height' => 292,  // Высота среднего изображения
                'img_large_width' => 744,  // Ширина большого изображения
                'img_large_height' => 492,  // Высота большого изображения
                'img_formats' => '.jpeg,.jpg,.png,.gif,.svg,.webp',  // Форматы изображения
                'img_min_width' => 300,  // Минимальная ширина изображения
                'img_min_height' => 150,  // Минимальная высота изображения
                'img_max_size' => 12,  // Размер изображения

                'author_id' => 1,
        	],
        ]);

        // $setting = PhotoSetting::where([
        //     'setting_id' => null,
        //     'setting_type' => null,
        // ])->first();

        // $entities = Entity::whereIn('alias', ['users', 'goods', 'raws', 'albums_categories'])->get();
    }
}
