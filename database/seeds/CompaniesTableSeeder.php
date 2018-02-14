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
                'company_email' => null,
		        'city_id' => '1',
		        'company_address' => 'ул. Шевцова, 5',
		        // 'company_inn' => '',
		        // 'kpp' => '',
		        // 'account_settlement' => '',
		        // 'account_correspondent' => '',
		        // 'bank' => '',
		        'director_user_id' => 4,  
                // 'admin_user_id' => '1',  
        	],
            [

                'company_name' => 'Оконная компания "Фенстер"',
                'company_phone' => '83952718765',
                // 'company_extra_phone' => '83952717775',
                'company_email' => null,
                'city_id' => '1',
                'company_address' => 'ул. Ленина, 5',
                // 'company_inn' => '',
                // 'kpp' => '',
                // 'account_settlement' => '',
                // 'account_correspondent' => '',
                // 'bank' => '',
                'director_user_id' => null,  
                // 'admin_user_id' => '1',  
            ],
            [

                'company_name' => 'Фирма "Автомобили"',
                'company_phone' => '83952712315',
                // 'company_extra_phone' => '83952717775',
                'company_email' => null,
                'city_id' => '1',
                'company_address' => 'ул. Есенина, 78б',
                // 'company_inn' => '',
                // 'kpp' => '',
                // 'account_settlement' => '',
                // 'account_correspondent' => '',
                // 'bank' => '',
                'director_user_id' => null,  
                // 'admin_user_id' => '1',  
            ],
        ]);
    }
}
