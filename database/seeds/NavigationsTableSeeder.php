<?php

use Illuminate\Database\Seeder;

class NavigationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
     DB::table('navigations')->insert([
      [
        'name' => 'Разделы управления сайтом',
        'site_id' => null, 
        'system_item' => 1,  
        'company_id' => null,
        'navigations_category_id' => null, 
        'display' => 1,         
      ],
      [
        'name' => 'Левый сайдбар',
        'site_id' => 1, 
        'system_item' => 1,  
        'company_id' => null,
        'navigations_category_id' => 1, 
        'display' => 1,         
      ],


    ]);
   }
 }
