<?php

use Illuminate\Database\Seeder;

class DepartmentSiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('department_site')->insert([
        	[
                'department_id' => 1,
                'site_id' => 2,
        	],
        	[
                'department_id' => 4,
                'site_id' => 2,
        	],

        ]);
    }
}
