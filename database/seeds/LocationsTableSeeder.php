<?php

use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{

	public function run()
	{
		DB::table('locations')->insert([
			[
				'city_id' => 1,
				'address' => 'ул. Шевцова, 5',
				'author_id' => 1,
				'country_id' => 1,
			],
		]);
	}
}
