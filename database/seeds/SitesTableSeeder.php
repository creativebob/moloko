<?php

use Illuminate\Database\Seeder;

class SitesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		 DB::table('sites')->insert([
        	[

		        'site_name' => 'Crm System',
		        'site_domen' => 'crmsystem.creativebob.ru',
		        'company_id' => null,
		        
        	],
        ]);
    }
}