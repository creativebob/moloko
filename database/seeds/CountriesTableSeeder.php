<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{

	public function run()
	{
		DB::table('countries')->insert([
			[
				'name' => 'Россия',
				'official' => 'Российская Федерация',
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'Белоруссия',
				'official' => 'Республика Беларусь',
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'Германия',
				'official' => 'Федеративная Республика Германия',
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'Китай',
				'official' => 'Китайская Народная Республика',
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'Турция',
				'official' => 'Турецкая Республика',
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
		]);
	}
}
