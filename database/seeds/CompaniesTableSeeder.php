<?php

use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->insert([
        	[

		        'company_name' => 'Воротная компания "Марс"',
		        'company_phone' => '83952717775',
		        // 'company_extra_phone' => '83952717775',
		        'city_id' => '1',
		        'company_address' => 'ул. Шевцова, 5',
		        // 'company_inn' => '',
		        // 'kpp' => '',
		        // 'account_settlement' => '',
		        // 'account_correspondent' => '',
		        // 'bank' => '',
		        'user_id' => '1',  
        	],
        ]);
    }
}
