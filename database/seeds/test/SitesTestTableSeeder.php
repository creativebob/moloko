<?php

use Illuminate\Database\Seeder;

class SitesTestTableSeeder extends Seeder
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
                'name' => 'Ворота "Марс"',
                'domain' => 'vorotamars.ru',
                'alias' => 'vorotamars',
                'company_id' => 1,
                'author_id' => 7,
                'system' => false,
                'moderation' => false,
                'api_token' => 'rqjl8HY4vh4EpU211BBaxT1zjooIv5k3s6uzaU0nE6xpeUtpe2n6ioBtpriu',
            ],
        ]);
    }
}
