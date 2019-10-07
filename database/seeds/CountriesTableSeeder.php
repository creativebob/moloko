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
				'vk_external_id' => 1,
			],
			[
				'name' => 'Белоруссия',
				'official' => 'Республика Беларусь',
				'system' => true,
				'author_id' => 1,
				'moderation' => false,
				'vk_external_id' => 3,
			],
			[
				'name' => 'Германия',
				'official' => 'Федеративная Республика Германия',
				'system' => true,
				'author_id' => 1,
				'moderation' => false,
				'vk_external_id' => 65,
			],
			[
				'name' => 'Китай',
				'official' => 'Китайская Народная Республика',
				'system' => true,
				'author_id' => 1,
				'moderation' => false,
				'vk_external_id' => 97,
			],
			[
				'name' => 'Турция',
				'official' => 'Турецкая Республика',
				'system' => true,
				'author_id' => 1,
				'moderation' => false,
				'vk_external_id' => 200,
			],
			[
				'name' => 'Казахстан',
				'official' => '',
				'system' => true,
				'author_id' => 1,
				'moderation' => false,
				'vk_external_id' => 4,
			],
		]);
	}
}
