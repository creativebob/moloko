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
        'navigation_name' => 'Разделы управления сайтом',
        'navigation_alias' => null,
        'site_id' => null, 
        'system_item' => 1,  
        'company_id' => null,
        'category_navigation_id' => 1,         
      ],
      [
        'navigation_name' => 'Левый сайдбар',
        'navigation_alias' => null,
        'site_id' => 1, 
        'system_item' => 1,  
        'company_id' => null,
        'category_navigation_id' => 2,         
      ],
      [
        'navigation_name' => 'Главное меню',
        'navigation_alias' => 'general',
        'site_id' => 2,  
        'system_item' => null, 
        'company_id' => 1,
        'category_navigation_id' => 2,            
      ],
      [
        'navigation_name' => 'Меню продукции',
        'navigation_alias' => 'products',
        'site_id' => 2, 
        'system_item' => null,  
        'company_id' => 1,
        'category_navigation_id' => 2,            
      ],

    ]);
   }
 }
