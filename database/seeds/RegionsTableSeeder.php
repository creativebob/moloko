<?php

use Illuminate\Database\Seeder;

class RegionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		 DB::table('regions')->insert([
        	[
		        'region_name' => 'Иркутская область',
		        'region_code' => 38,
		        'region_vk_external_id' => 1127513,
                'system_item' => 1,
     	    ],
            [
                'region_name' => 'Бурятия',
                'region_code' => null,
                'region_vk_external_id' => null,
                'system_item' => 1,

            ],
            [
                'region_name' => 'Красноярский край',
                'region_code' => null,
                'region_vk_external_id' => null,
                'system_item' => 1,
            ],
            [
                'region_name' => 'Забайкальский край',
                'region_code' => null,
                'region_vk_external_id' => null,
                'system_item' => 1,
            ],
        ]);
    }
}
