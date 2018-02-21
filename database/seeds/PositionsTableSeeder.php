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
		        'position_name' => 'Директор',
                'page_id' => 6,
		        'direction_status' => 1,
		        'company_id' => null,
                'system_item' => null,
                'author_id' => 1,
        	],
            [
                'position_name' => 'Менеджер',
                'page_id' => 6,
                'direction_status' => null,
                'company_id' => null,
                'system_item' => null,
                'author_id' => 1,
            ],
            [
                'position_name' => 'Администратор',
                'page_id' => 6,
                'direction_status' => null,
                'company_id' => null,
                'system_item' => null,
                'author_id' => 1,
            ],
        	[
		        'position_name' => 'Web-разработчик',
                'page_id' => 6,
		        'direction_status' => null,
		        'company_id' => 1,
                'system_item' => null,
                'author_id' => 4,
        	],
            [
                'position_name' => 'Сторож',
                'page_id' => 6,
                'direction_status' => null,
                'company_id' => 2,
                'system_item' => null,
                'author_id' => 14,
            ],
        ]);
    }
}
