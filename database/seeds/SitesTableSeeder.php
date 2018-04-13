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
                'moderation' => null,
                'api_token' => str_random(60),
        	],
            [
                'site_name' => 'Ворота "Марс"',
                'site_domen' => 'vorotamars.ru',
                'site_alias' => 'vorotamars',
                'company_id' => 1,
                'author_id' => 4,
                'system_item' => null,
                'moderation' => null,
                'api_token' => 'rqjl8HY4vh4EpU211BBaxT1zjooIv5k3s6uzaU0nE6xpeUtpe2n6ioBtpriu',
            ],
            [
                'site_name' => 'Ибаный',
                'site_domen' => 'lol.ru',
                'site_alias' => 'lol',
                'company_id' => 1,
                'author_id' => 7,
                'system_item' => null,
                'moderation' => 1,
                'api_token' => str_random(60),
            ],
            [
                'site_name' => 'Фенстер',
                'site_domen' => 'f-okna.ru',
                'site_alias' => 'f-okna',
                'company_id' => 2,
                'author_id' => 14,
                'system_item' => null,
                'moderation' => null,
                'api_token' => str_random(60),
            ],
        ]);
    }
}
