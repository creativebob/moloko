<?php

use Illuminate\Database\Seeder;

class UnitsTableSeeder extends Seeder
{

	public function run()
	{
		DB::table('units')->insert([
			[
				'name' => 'Штука', 
				'preview' => 'шт', 
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'Рулон', 
				'preview' => 'рул', 
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'Квадратный метр', 
				'preview' => 'м2', 
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'Погонный метр', 
				'preview' => 'пог. м', 
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'Упаковка', 
				'preview' => 'уп', 
				'system_item' => 1, 
				'author_id' => 1, 
				'moderation' => null, 
			],
		]);
	}
}
