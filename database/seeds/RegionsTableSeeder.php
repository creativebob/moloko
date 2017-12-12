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
		        'region_code' => '38',
		        'region_vk_external_id' => '1127513',
		        
        	],
        ]);
    }
}
