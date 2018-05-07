<?php

use Illuminate\Database\Seeder;

class ScheduleEntityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

		DB::table('schedule_entity')->insert([
			[
				'schedule_id' => 1, 
				'entity_id' => 1, 
				'entity' => 'companies', 
			],
			[
				'schedule_id' => 2, 
				'entity_id' => 2, 
				'entity' => 'companies', 
			],
			[
				'schedule_id' => 3, 
				'entity_id' => 3, 
				'entity' => 'companies', 
			],
			[
				'schedule_id' => 4, 
				'entity_id' => 4, 
				'entity' => 'companies', 
			],

		]);
    }
}
