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
		        'domain' => 'crmsystem.vkmars.ru',
                'alias' => 'crmsystem',
		        'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
                'api_token' => \Str::random(60),
        	],

            // [
            //     'name' => 'vtoroy',
            //     'domain' => 'vtoroy',
            //     'alias' => 'vtoroy',
            //     'company_id' => null,
            //     'author_id' => 1,
            //     'system' => true,
            //     'moderation' => false,
            //     'api_token' => str_random(60),
            // ],

        ]);
    }
}
