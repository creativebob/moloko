<?php

use Illuminate\Database\Seeder;

class PositionsTestTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('positions')->insert([
        	[
		        'name' => 'Web-разработчик',
                'page_id' => 32,
		        'direction' => null,
		        'company_id' => 1,
                'system_item' => null,
                'author_id' => 1,
                'sector_id' => 8,
        	],
            [
                'name' => 'Технический специалист',
                'page_id' => 32,
                'direction' => 1,
                'company_id' => 1,
                'system_item' => null,
                'author_id' => 1,
                'sector_id' => null,
            ],
        ]);
    }
}
