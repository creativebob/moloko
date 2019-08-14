<?php

use Illuminate\Database\Seeder;

class WorktimesTestTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('worktimes')->insert([
			// ВК "Марс" (09:00-18:00)
			[
				'schedule_id' => 1, 
				'weekday' => 1, 
				'worktime_begin' => 32400, 
				'worktime_interval' => 32400, 
				'company_id' => 1, 
				'system' => false,
				'author_id' => 1, 
				'moderation' => false,
			],
			[
				'schedule_id' => 1, 
				'weekday' => 2, 
				'worktime_begin' => 32400, 
				'worktime_interval' => 32400, 
				'company_id' => 1, 
				'system' => false,
				'author_id' => 1, 
				'moderation' => false,
			],
			[
				'schedule_id' => 1, 
				'weekday' => 3, 
				'worktime_begin' => 32400, 
				'worktime_interval' => 32400, 
				'company_id' => 1, 
				'system' => false,
				'author_id' => 1, 
				'moderation' => false,
			],
			[
				'schedule_id' => 1, 
				'weekday' => 4, 
				'worktime_begin' => 32400, 
				'worktime_interval' => 32400, 
				'company_id' => 1, 
				'system' => false,
				'author_id' => 1, 
				'moderation' => false,
			],
			[
				'schedule_id' => 1, 
				'weekday' => 5, 
				'worktime_begin' => 32400, 
				'worktime_interval' => 32400, 
				'company_id' => 1, 
				'system' => false,
				'author_id' => 1, 
				'moderation' => false,
			],

		]);
    }
}
