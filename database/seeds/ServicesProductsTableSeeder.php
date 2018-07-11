<?php

use Illuminate\Database\Seeder;

class ServicesProductsTableSeeder extends Seeder
{

      public function run()
      {
            DB::table('services_products')->insert([
                  [
                        'name' => 'Услуга какая то',
                        'services_category_id' => 2,
                        'company_id' => 1, 
                        'author_id' => 4, 
                        'unit_id' => 26,
                        'display' => 1,
                  ],


            ]);
      }
}
