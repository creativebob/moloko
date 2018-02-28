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
		        'phone' => '83952717775',
		        // 'company_extra_phone' => '83952717775',
                'email' => null,
		        'city_id' => '1',
		        'address' => 'ул. Шевцова, 5',
		        // 'inn' => '',
		        // 'kpp' => '',
		        // 'account_settlement' => '',
		        // 'account_correspondent' => '',
		        // 'bank' => '',
		        'director_user_id' => 4,  
                // 'admin_user_id' => '1', 
                'city_id' => 1,
                'moderation' => null,
                'sector_id' => 8,
        	],
            [

                'company_name' => 'Оконная компания "Фенстер"',
                'phone' => '83952718765',
                // 'company_extra_phone' => '83952717775',
                'email' => null,
                'city_id' => '1',
                'address' => 'ул. Ленина, 5',
                // 'inn' => '',
                // 'kpp' => '',
                // 'account_settlement' => '',
                // 'account_correspondent' => '',
                // 'bank' => '',
                'director_user_id' => null,  
                // 'admin_user_id' => '1',  
                'city_id' => 1,
                'moderation' => null,
                'sector_id' => 9,
            ],
            [

                'company_name' => 'Фирма "Автомобили"',
                'phone' => '83952712315',
                // 'company_extra_phone' => '83952717775',
                'email' => null,
                'city_id' => '1',
                'address' => 'ул. Есенина, 78б',
                // 'inn' => '',
                // 'kpp' => '',
                // 'account_settlement' => '',
                // 'account_correspondent' => '',
                // 'bank' => '',
                'director_user_id' => null,  
                // 'admin_user_id' => '1',  
                'city_id' => 1,
                'moderation' => 1,
                'sector_id' => 12,
            ],
        ]);
    }
}
