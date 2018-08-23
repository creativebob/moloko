<?php

use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{

	public function run()
	{
		DB::table('locations')->insert([
			[
				'city_id' => 1,
				'address' => 'Совершенно секретно',
				'author_id' => 1,
				'country_id' => 1,
			],
		]);
	}
}
