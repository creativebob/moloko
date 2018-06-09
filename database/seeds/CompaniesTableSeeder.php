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

		        'name' => 'Воротная компания "Марс"',
                'alias' => 'vkmars',
		        'phone' => '83952717775',
		        // 'extra_phone' => '83952717775',
                'email' => null,
		        'location_id' => 1,
		        // 'inn' => '',
		        // 'kpp' => '',
		        // 'account_settlement' => '',
		        // 'account_correspondent' => '',
		        // 'bank' => '',
		        'director_user_id' => 4,  
                // 'admin_user_id' => '1', 
                'moderation' => null,
                'sector_id' => 8,
                'author_id' => 1,
        	],
            [

                'name' => 'Оконная компания "Фенстер"',
                'alias' => 'fenster',
                'phone' => '83952718765',
                // 'extra_phone' => '83952717775',
                'email' => null,
                'location_id' => 2,
                // 'inn' => '',
                // 'kpp' => '',
                // 'account_settlement' => '',
                // 'account_correspondent' => '',
                // 'bank' => '',
                'director_user_id' => null,  
                // 'admin_user_id' => '1',  
                'moderation' => null,
                'sector_id' => 9,
                'author_id' => 1,
            ],
            [

                'name' => 'Фирма "Автомобили"',
                'alias' => 'automobile',
                'phone' => '83952712315',
                // 'extra_phone' => '83952717775',
                'email' => null,
                'location_id' => 3,
                // 'inn' => '',
                // 'kpp' => '',
                // 'account_settlement' => '',
                // 'account_correspondent' => '',
                // 'bank' => '',
                'director_user_id' => null,  
                // 'admin_user_id' => '1',  
                'moderation' => 1,
                'sector_id' => 12,
                'author_id' => 1,
            ],
            [

                'name' => 'Шторка',
                'alias' => 'storka',
                'phone' => '89149266771',
                // 'extra_phone' => '83952717775',
                'email' => 'akasha_07@mail.ru',
                'location_id' => 4,
                // 'inn' => '',
                // 'kpp' => '',
                // 'account_settlement' => '',
                // 'account_correspondent' => '',
                // 'bank' => '',
                'director_user_id' => 15,  
                // 'admin_user_id' => '1',  
                'moderation' => null,
                'sector_id' => 12,
                'author_id' => 1,
            ],
        ]);
    }
}
