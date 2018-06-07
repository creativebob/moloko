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
      [
        'name' => 'Навигация по сайту',
        'site_id' => 2,  
        'system_item' => null, 
        'company_id' => 1,
        'navigations_category_id' => 1, 
        'display' => 1,            
      ],
      [
        'name' => 'Меню продукции',
        'site_id' => 2, 
        'system_item' => null,  
        'company_id' => 1,
        'navigations_category_id' => 2,  
        'display' => 1,           
      ],
      [
        'name' => 'Меню в футере',
        'site_id' => 2, 
        'system_item' => null,  
        'company_id' => 1,
        'navigations_category_id' => 4,  
        'display' => 1,           
      ],
      [
        'name' => 'Соц-медиа',
        'site_id' => 2, 
        'system_item' => null,  
        'company_id' => 1,
        'navigations_category_id' => 3,  
        'display' => 1,           
      ],
      [
        'name' => 'Навигация по сайту',
        'site_id' => 5,  
        'system_item' => null, 
        'company_id' => 4,
        'navigations_category_id' => 1, 
        'display' => 1,            
      ],

    ]);
   }
 }
