<?php

use Illuminate\Database\Seeder;

use App\Entity;
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

                'photo_settings_id' => null,
                'photo_settings_type' => null,

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

        // $setting = PhotoSetting::where([
        //     'setting_id' => null,
        //     'setting_type' => null,
        // ])->first();

        // $entities = Entity::whereIn('alias', ['users', 'goods', 'raws', 'albums_categories'])->get();
    }
}
