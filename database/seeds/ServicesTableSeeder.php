<?php

use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('services')->insert([
            [
                'name' => 'Первый артикул',
                'services_product_id' => 1,
                'cost' => null,
                'price' => null,
                'company_id' => 1, 
                'manufacturer_id' => null,
                'author_id' => 1, 
            ],
            [
                'name' => 'Второй артикул',
                'services_product_id' => 1,
                'cost' => null,
                'price' => null,
                'company_id' => 1, 
                'manufacturer_id' => null,
                'author_id' => 1, 
            ],


    	]);
    }
}
