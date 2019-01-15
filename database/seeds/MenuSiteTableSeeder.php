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
		        'menu_id' => 1,
		        'site_id' => 1,
        	],
            [
                'menu_id' => 2,
		        'site_id' => 1,
            ],

        ]);
    }
}
