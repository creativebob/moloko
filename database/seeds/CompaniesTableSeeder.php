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
                'company_alias' => 'vkmars',
		        'phone' => '83952717775',
		        // 'company_extra_phone' => '83952717775',
                'email' => null,
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
                'author_id' => 1,
        	],
            [

                'company_name' => 'Оконная компания "Фенстер"',
                'company_alias' => 'fenster',
                'phone' => '83952718765',
                // 'company_extra_phone' => '83952717775',
                'email' => null,
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
                'author_id' => 1,
            ],
            [

                'company_name' => 'Фирма "Автомобили"',
                'company_alias' => 'automobile',
                'phone' => '83952712315',
                // 'company_extra_phone' => '83952717775',
                'email' => null,
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
                'author_id' => 1,
            ],
            [

                'company_name' => 'Шторка',
                'company_alias' => 'storka',
                'phone' => '83952712318',
                // 'company_extra_phone' => '83952717775',
                'email' => null,
                'address' => 'ул. Шевцова, 5',
                // 'inn' => '',
                // 'kpp' => '',
                // 'account_settlement' => '',
                // 'account_correspondent' => '',
                // 'bank' => '',
                'director_user_id' => null,  
                // 'admin_user_id' => '1',  
                'city_id' => 1,
                'moderation' => null,
                'sector_id' => 12,
                'author_id' => 1,
            ],
        ]);
    }
}
