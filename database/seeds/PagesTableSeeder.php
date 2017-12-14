<?php

use Illuminate\Database\Seeder;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		 DB::table('pages')->insert([
        	[
		        'page_name' => 'Первая',
                'site_id' => '1',
		        'page_title' => 'Первая',
		        'page_description' => 'лололо',
                'page_alias' => '/first',
        	],
        ]);
    }
}
