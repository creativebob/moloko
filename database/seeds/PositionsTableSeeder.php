<?php

use Illuminate\Database\Seeder;

class PositionsTableSeeder extends Seeder
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
		        'name' => 'Директор',
                'page_id' => 31,
		        'direction_status' => 1,
		        'company_id' => null,
                'system_item' => null,
                'author_id' => 1,
                'sector_id' => null,
        	],
            [
                'name' => 'Менеджер',
                'page_id' => 31,
                'direction_status' => null,
                'company_id' => null,
                'system_item' => null,
                'author_id' => 1,
                'sector_id' => null,
            ],
            [
                'name' => 'Администратор',
                'page_id' => 31,
                'direction_status' => null,
                'company_id' => null,
                'system_item' => null,
                'author_id' => 1,
                'sector_id' => null,
            ],
        ]);
    }
}
