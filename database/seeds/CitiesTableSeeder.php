<?php

use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->insert([
        	[
		        'name' => 'Иркутск',
                'alias' => 'irkutsk',
		        // 'area_id' => '',
		        'region_id' => 1,
		        'code' => 83952,
		        'vk_external_id' => 57,
                'system_item' => 1,
        	],

        ]);
    }
}
