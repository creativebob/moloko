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
                'name' => 'Моя компания',
                'phone' => 89000000000, 
                'location_id' => 1,
                'sector_id' => 1,
                'system_item' => 1, 
                'author_id' => 1, 
                'moderation' => null, 
                'alias' => 'mycompany',
            ],
        ]);
    }
}
