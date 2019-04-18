<?php

use Illuminate\Database\Seeder;

class ChallengesTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('challenges_types')->insert([
        	[
		        'name' => 'Встреча',
		        'description' => null,
                'author_id' => 1,
        	],
        	[
		        'name' => 'Перезвонить',
		        'description' => null,
                'author_id' => 1,
        	],
        	[
		        'name' => 'Замер',
		        'description' => null,
                'author_id' => 1,
        	],
        ]);
    }
}
