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
		        'page_name' => 'Населенные пункты',
                'site_id' => '1',
		        'page_title' => 'Населенные пункты',
		        'page_description' => 'Области, районы и города',
                'page_alias' => '/cities',
        	],
            [
                'page_name' => 'Филиалы',
                'site_id' => '1',
                'page_title' => 'Филиалы',
                'page_description' => 'Филиалы, отделы и должности',
                'page_alias' => '/departments',
            ],
        ]);
    }
}
