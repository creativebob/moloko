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
		        'site_domen' => 'crmsystem.vkmars.ru',
		        'company_id' => null,
		        
        	],
            [
                'site_name' => 'Ворота "Марс"',
                'site_domen' => 'vorotamars.ru',
                'company_id' => 1,
                
            ],
        ]);
    }
}
