<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{

	public function run()
	{
		DB::table('countries')->insert([
			[
				'name' => 'Россия', 
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'Белоруссия', 
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'Германия', 
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
		]);
	}
}
