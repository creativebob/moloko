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
				'name' => 'Австралия', 
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'Австрия', 
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'Азербайджан', 
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
		]);
	}
}
