<?php

use Illuminate\Database\Seeder;

class PlacesTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('places_types')->insert([
        	[
		        'name' => 'Производственное помещение',
		        'description' => '',
		        'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
        	],
        	[
		        'name' => 'Склад',
		        'description' => '',
		        'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
        	],
        	[
		        'name' => 'Офис',
		        'description' => '',
		        'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
        	],
        	[
		        'name' => 'Жилое помещение',
		        'description' => '',
		        'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
        	],
            [
                'name' => 'Гараж',
                'description' => '',
                'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
            ],
        ]);
    }
}
