<?php

use Illuminate\Database\Seeder;

class MenuSiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menu_site')->insert([
        	[
		        'menu_id' => 18,
		        'site_id' => 1,
        	],
            [
                'menu_id' => 19,
		        'site_id' => 1,
            ],
            [
                'menu_id' => 18,
		        'site_id' => 2,
            ],
            [
                'menu_id' => 19,
		        'site_id' => 2,
            ],
            [
                'menu_id' => 20,
		        'site_id' => 2,
            ],
            [
                'menu_id' => 18,
                'site_id' => 5,
            ],
            [
                'menu_id' => 19,
                'site_id' => 5,
            ],
            [
                'menu_id' => 20,
                'site_id' => 5,
            ],
        ]);
    }
}
