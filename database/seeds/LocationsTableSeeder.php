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
			[
				'city_id' => 1,
				'address' => 'ул. Кекная, 5',
				'author_id' => 1,
				'country_id' => 1,
			],
			[
				'city_id' => 1,
				'address' => 'ул. Лольная, 5',
				'author_id' => 1,
				'country_id' => 1,
			],
			[
				'city_id' => 2,
				'address' => 'ул. Шевцова, 5',
				'author_id' => 1,
				'country_id' => 1,
			],
			[
				'city_id' => 1,
				'address' => 'ул. Шевцова, 5, оф. 202',
				'author_id' => 1,
				'country_id' => 1,
			],
			[
				'city_id' => 2,
				'address' => 'ул. Ленина, 45б',
				'author_id' => 1,
				'country_id' => 1,
			],
			[
				'city_id' => 4,
				'address' => 'ул. Мира, 11',
				'author_id' => 1,
				'country_id' => 1,
			],
			[
				'city_id' => 1,
				'address' => 'ул. Шевцова, 61',
				'author_id' => 1,
				'country_id' => 1,
			],
		]);
	}
}
