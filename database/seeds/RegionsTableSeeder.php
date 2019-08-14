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
		        'name' => 'Иркутская область',
		        'code' => 38,
		        'vk_external_id' => 1127513,
                'system' => true,
     	    ],
        ]);
    }
}
