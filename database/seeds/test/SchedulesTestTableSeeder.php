<?php

use Illuminate\Database\Seeder;

class SchedulesTestTableSeeder extends Seeder
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
				'name' => 'График работы для ВК Марс', 
				'company_id' => 1, 
				'system_item' => null, 
				'author_id' => 1, 
				'moderation' => null, 
			],

		]);
    }
}
