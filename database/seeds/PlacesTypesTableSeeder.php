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
                'system' => true,
                'moderation' => false,
        	],
        	[
		        'name' => 'Склад',
		        'description' => '',
		        'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
        	],
        	[
		        'name' => 'Офис',
		        'description' => '',
		        'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
        	],
        	[
		        'name' => 'Жилое помещение',
		        'description' => '',
		        'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
        	],
            [
                'name' => 'Гараж',
                'description' => '',
                'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
            ],
        ]);
    }
}
