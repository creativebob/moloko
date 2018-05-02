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
		        'name' => 'Crm System',
		        'domen' => 'crmsystem.vkmars.ru',
                'alias' => 'crmsystem',
		        'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
                'api_token' => str_random(60),
        	],
            [
                'name' => 'Ворота "Марс"',
                'domen' => 'vkmars',
                'alias' => 'vkmars',
                'company_id' => 1,
                'author_id' => 4,
                'system_item' => null,
                'moderation' => null,
                'api_token' => 'rqjl8HY4vh4EpU211BBaxT1zjooIv5k3s6uzaU0nE6xpeUtpe2n6ioBtpriu',
            ],
            [
                'name' => 'Ибаный',
                'domen' => 'lol.ru',
                'alias' => 'lol',
                'company_id' => 1,
                'author_id' => 7,
                'system_item' => null,
                'moderation' => 1,
                'api_token' => str_random(60),
            ],
            [
                'name' => 'Фенстер',
                'domen' => 'f-okna.ru',
                'alias' => 'f-okna',
                'company_id' => 2,
                'author_id' => 14,
                'system_item' => null,
                'moderation' => null,
                'api_token' => str_random(60),
            ],
            [
                'name' => 'Шторка',
                'domen' => 'shtorka',
                'alias' => 'storka',
                'company_id' => 4,
                'author_id' => 14,
                'system_item' => null,
                'moderation' => null,
                'api_token' => 'Y0M3jVi8tgRmUalSTe5zbLpBsHW0324xOyimn0MUa5zaoVrtuEfv8iskaV2x',
            ],
        ]);
    }
}
