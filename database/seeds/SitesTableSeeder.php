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
                'site_alias' => 'crmsystem',
		        'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
		        
        	],
            [
                'site_name' => 'Ворота "Марс"',
                'site_domen' => 'vorotamars.ru',
                'site_alias' => 'vorotamars',
                'company_id' => 1,
                'author_id' => 4,
                'system_item' => null,
                
            ],
        ]);
    }
}
