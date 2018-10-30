<?php

use Illuminate\Database\Seeder;

class PrioritiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('priorities')->insert([
        	[
		        'name' => 'Обычный',
                'description' => 'Самый обыкновенный ни чем не примечательный приоритет',
                'author_id' => 1,
        	],
            [
                'name' => 'Важный',
                'description' => 'Важный приоритет',
                'author_id' => 1,
            ],
            [
                'name' => 'Очень важный!',
                'description' => 'Срочно, срочно - немедленно! Самый важный приоритет',
                'author_id' => 1,
            ],
        ]);
    }
}
