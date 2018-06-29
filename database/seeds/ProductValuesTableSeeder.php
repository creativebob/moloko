<?php

use Illuminate\Database\Seeder;

class ProductValuesTableSeeder extends Seeder
{

	public function run()
	{
		$mass[] = [
			'product_id' => 1,
			'entity_id' => 6,
			'entity' => 'metrics',
			'value' => 100,
		];
		for ($i=7; $i <= 41; $i++) { 
			$mass[] = [
				'product_id' => $i,
				'entity_id' => 6,
				'entity' => 'metrics',
				'value' => 100,
			];
		}

		for ($i=7; $i <= 41; $i++) { 
			$mass[] = [
				'product_id' => $i,
				'entity_id' => 3,
				'entity' => 'metrics',
				'value' => 280,
			];
		}

		DB::table('product_values')->insert($mass);
	}
}
