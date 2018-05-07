<?php

use Illuminate\Database\Seeder;

class SchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('schedules')->insert([
			[
				'name' => 'График работы для Автомобили', 
				'company_id' => 1, 
				'system_item' => null, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'График работы для ВК Марс', 
				'company_id' => 2, 
				'system_item' => null, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'График работы для Фенстер', 
				'company_id' => 3, 
				'system_item' => null, 
				'author_id' => 1, 
				'moderation' => null, 
			],
			[
				'name' => 'График работы для Шторка', 
				'company_id' => 4, 
				'system_item' => null, 
				'author_id' => 1, 
				'moderation' => null, 
			],

		]);
    }
}
