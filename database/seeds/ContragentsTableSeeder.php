<?php

use Illuminate\Database\Seeder;

class ContragentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contragents')->insert([
            [
                'company_id' => 1,
                'contragent_id' => 2,
                'client_status' => 1,
                'vendor_status' => 1,
                'author_id' => 1,
                'manufacturer_status' => 1,
            ],
            [
                'company_id' => 1,
                'contragent_id' => 3,
                'client_status' => 1,
                'vendor_status' => null,
                'author_id' => 1,
                'manufacturer_status' => null,
            ],
            [
                'company_id' => 1,
                'contragent_id' => 4,
                'client_status' => 1,
                'vendor_status' => null,
                'author_id' => 1,
                'manufacturer_status' => 1,
            ],
            [
                'company_id' => 2,
                'contragent_id' => 4,
                'client_status' => null,
                'vendor_status' => 1,
                'author_id' => 1,
                'manufacturer_status' => null,
            ],
            [
                'company_id' => 2,
                'contragent_id' => 1,
                'client_status' => 1,
                'vendor_status' => null,
                'author_id' => 1,
                'manufacturer_status' => null,
            ], 
            [
                'company_id' => 1,
                'contragent_id' => 1,
                'client_status' => null,
                'vendor_status' => null,
                'author_id' => 1,
                'manufacturer_status' => 1,
            ], 
        ]);
    }
}
