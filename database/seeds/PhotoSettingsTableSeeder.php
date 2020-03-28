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

                'img_small_width' => 150,
                'img_small_height' => 99,
                'img_medium_width' => 440,
                'img_medium_height' => 292,
                'img_large_width' => 744,
                'img_large_height' => 492,
                'img_formats' => 'jpeg,jpg,png,gif,svg,webp',
                'img_min_width' => 300,
                'img_min_height' => 150,
                'img_max_size' => 12000,

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
