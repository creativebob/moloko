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
                'page_id' => '1',
		        'direction_status' => null,
		        'company_id' => null,
        	],
            [
                'position_name' => 'Менеджер',
                'page_id' => '1',
                'direction_status' => '1',
                'company_id' => null,
            ],
        	[
		        'position_name' => 'Web-разработчик',
                'page_id' => '1',
		        'direction_status' => null,
		        'company_id' => 1,
        	],
        ]);
    }
}
