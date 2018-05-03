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
        'alias' => null,
        'site_id' => null, 
        'system_item' => 1,  
        'company_id' => null,
        'navigations_category_id' => 1, 
        'display' => 1,         
      ],
      [
        'name' => 'Левый сайдбар',
        'alias' => null,
        'site_id' => 1, 
        'system_item' => 1,  
        'company_id' => null,
        'navigations_category_id' => 2, 
        'display' => 1,         
      ],
      [
        'name' => 'Главное меню',
        'alias' => 'general',
        'site_id' => 2,  
        'system_item' => null, 
        'company_id' => 1,
        'navigations_category_id' => 2, 
        'display' => 1,            
      ],
      [
        'name' => 'Меню продукции',
        'alias' => 'products',
        'site_id' => 2, 
        'system_item' => null,  
        'company_id' => 1,
        'navigations_category_id' => 2,  
        'display' => 1,           
      ],
      [
        'name' => 'Структура',
        'alias' => 'footer',
        'site_id' => 2, 
        'system_item' => null,  
        'company_id' => 1,
        'navigations_category_id' => 2,  
        'display' => 1,           
      ],
      [
        'name' => 'Соц-медиа',
        'alias' => 'media',
        'site_id' => 2, 
        'system_item' => null,  
        'company_id' => 1,
        'navigations_category_id' => 2,  
        'display' => 1,           
      ],

    ]);
   }
 }
