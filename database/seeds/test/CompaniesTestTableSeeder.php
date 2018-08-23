<?php

use Illuminate\Database\Seeder;

class CompaniesTestTableSeeder extends Seeder
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
                'location_id' => 2,
                // 'inn' => '',
                // 'kpp' => '',
                // 'account_settlement' => '',
                // 'account_correspondent' => '',
                // 'bank' => '',
                'director_user_id' => 4,  
                // 'admin_user_id' => '1', 
                'moderation' => null,
                'sector_id' => 24,
                'author_id' => 1,
            ],
        ]);
    }
}
