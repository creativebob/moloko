<?php

use Illuminate\Database\Seeder;

class MenuSiteTestTableSeeder extends Seeder
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
                'menu_id' => 31,
		        'site_id' => 2,
            ],
            [
                'menu_id' => 32,
		        'site_id' => 2,
            ],
            [
                'menu_id' => 33,
		        'site_id' => 2,
            ],
        ]);
    }
}
