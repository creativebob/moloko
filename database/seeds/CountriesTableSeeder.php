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
				'system' => true,
				'author_id' => 1,
				'moderation' => false,
			],
			[
				'name' => 'Белоруссия',
				'official' => 'Республика Беларусь',
				'system' => true,
				'author_id' => 1,
				'moderation' => false,
			],
			[
				'name' => 'Германия',
				'official' => 'Федеративная Республика Германия',
				'system' => true,
				'author_id' => 1,
				'moderation' => false,
			],
			[
				'name' => 'Китай',
				'official' => 'Китайская Народная Республика',
				'system' => true,
				'author_id' => 1,
				'moderation' => false,
			],
			[
				'name' => 'Турция',
				'official' => 'Турецкая Республика',
				'system' => true,
				'author_id' => 1,
				'moderation' => false,
			],
		]);
	}
}
